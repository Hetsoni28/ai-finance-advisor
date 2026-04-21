<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\AiChat;
use App\Models\Income;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\UserFinancialProfile;
use App\Services\Ai\GeminiService;
use App\Services\Ai\MarketDataService;
use App\Services\Ai\ContextBuilderService;
use App\Services\Ai\IntentClassifier;
use App\Services\Ai\ResponseProcessor;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AiChatController extends Controller
{
    private GeminiService $gemini;
    private MarketDataService $market;
    private ContextBuilderService $contextBuilder;
    private IntentClassifier $classifier;
    private ResponseProcessor $processor;

    public function __construct(
        GeminiService $gemini,
        MarketDataService $market,
        ContextBuilderService $contextBuilder,
        IntentClassifier $classifier,
        ResponseProcessor $processor
    ) {
        $this->gemini         = $gemini;
        $this->market         = $market;
        $this->contextBuilder = $contextBuilder;
        $this->classifier     = $classifier;
        $this->processor      = $processor;
    }

    /**
     * SHOW AI CHAT INTERFACE
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $user = Auth::user();

        // Fetch last 50 messages for display
        $chats = AiChat::where('user_id', $userId)
            ->latest('id')
            ->take((int) config('financeai.context.max_display_messages', 50))
            ->get()
            ->reverse();

        // Initialize with welcome message for first-time users
        if ($chats->isEmpty()) {
            $welcomeMessage = $this->generateWelcomeMessage();
            $sessionId = 'chat_' . now()->format('Ymd');

            $chat = AiChat::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'sender'     => AiChat::SENDER_AI,
                'message'    => $welcomeMessage,
                'tokens'     => intval(strlen($welcomeMessage) / 4),
            ]);

            $chats = collect([$chat]);
        }

        // Build real context data for sidebar
        $snapshot = $user->financial_snapshot;
        $context = [
            'net_worth'    => '₹' . number_format($snapshot['net_worth'], 0),
            'monthly_burn' => '₹' . number_format($snapshot['monthly_expense'], 0),
            'saving_rate'  => $snapshot['saving_rate'] . '%',
            'risk_status'  => $this->getHealthLabel($snapshot['saving_rate']),
            'runway'       => $snapshot['runway_months'] . ' mo',
            'last_sync'    => now()->setTimezone('Asia/Kolkata')->format('h:i:s A'),
        ];

        // Market data for sidebar (Pro+ only)
        $marketData = null;
        if ($user->hasPlan('pro')) {
            try {
                $marketData = $this->market->getSidebarData();
            } catch (\Throwable $e) {
                Log::warning('Market sidebar data failed: ' . $e->getMessage());
            }
        }

        // Active goals summary
        $activeGoals = $user->financialGoals()->active()->limit(3)->get();

        // Financial profile status
        $hasProfile = UserFinancialProfile::where('user_id', $userId)->exists();

        // AI engine status
        $aiMode = $this->gemini->isAvailable() ? 'gemini' : 'heuristic';

        // Daily usage count
        $todayUsage = AiChat::where('user_id', $userId)
            ->where('sender', AiChat::SENDER_USER)
            ->whereDate('created_at', today())
            ->count();

        $planSlug = $user->current_plan_slug;
        $dailyLimit = (int) config("financeai.limits.{$planSlug}", config('financeai.limits.free', 20));

        return view('user.ai-chat', compact(
            'chats', 'context', 'marketData', 'activeGoals',
            'hasProfile', 'aiMode', 'todayUsage', 'dailyLimit'
        ));
    }

    /**
     * HANDLE USER MESSAGE (AJAX) — Full AI Pipeline
     */
    public function sendMessage(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['reply' => 'Session expired. Please login again.'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userId    = Auth::id();
        $user      = Auth::user();
        $message   = trim($request->message);
        $sessionId = 'chat_' . now()->format('Ymd');

        // Rate limiting
        $planSlug = $user->current_plan_slug;
        $dailyLimit = (int) config("financeai.limits.{$planSlug}", config('financeai.limits.free', 20));

        $todayCount = AiChat::where('user_id', $userId)
            ->where('sender', AiChat::SENDER_USER)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= $dailyLimit) {
            return response()->json([
                'reply'      => "**Daily Limit Reached** 🔒\n\nYou've used all {$dailyLimit} AI queries for today on your **{$user->current_plan_name}** plan.\n\nUpgrade to Pro for 200 daily queries and live market data.",
                'confidence' => 'high',
                'confidence_label' => '🟢 System Message',
                'sources'    => [],
            ]);
        }

        try {
            DB::beginTransaction();

            // 1. Save USER message
            AiChat::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'sender'     => AiChat::SENDER_USER,
                'message'    => $message,
                'tokens'     => intval(strlen($message) / 4),
            ]);

            // 2. Classify intent
            $intentData = $this->classifier->classify($message);

            // 3. Gate market features for free users
            if ($intentData['needs_market'] && !$user->hasPlan('pro')) {
                $intentData['needs_market'] = false;
            }

            // 4. Build RAG context
            $context = $this->contextBuilder->build($userId, $intentData);

            // 5. Track data sources used
            $sources = ['User Database'];
            if ($intentData['needs_market'] && $user->hasPlan('pro')) {
                $sources[] = 'CoinGecko API';
            }

            // 6. Generate response (LLM or fallback)
            $reply = '';
            $tokensUsed = 0;

            if ($this->gemini->isAvailable()) {
                // === GEMINI LLM PATH ===
                $systemPrompt = config('financeai.system_prompt', '');
                $result = $this->gemini->generate($systemPrompt, $context, $message);

                if ($result['success']) {
                    $processed = $this->processor->process($result['content'], $intentData, $sources);
                    $reply = $processed['content'];
                    $tokensUsed = $result['tokens_used'];
                } else {
                    // LLM failed — use fallback
                    Log::warning('Gemini failed, using fallback: ' . ($result['error'] ?? 'unknown'));
                    $reply = $this->fallbackEngine($userId, $message, $intentData);
                    $processed = $this->processor->process($reply, $intentData, ['User Database']);
                    $reply = $processed['content'];
                }
            } else {
                // === FALLBACK ENGINE (NO API KEY) ===
                $reply = $this->fallbackEngine($userId, $message, $intentData);
                $processed = $this->processor->process($reply, $intentData, ['User Database']);
                $reply = $processed['content'];
            }

            // 7. Save AI response
            AiChat::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'sender'     => AiChat::SENDER_AI,
                'message'    => $reply,
                'tokens'     => max($tokensUsed, intval(strlen($reply) / 4)),
            ]);

            // 8. Prune old messages
            $keepThreshold = AiChat::where('user_id', $userId)->latest('id')->skip(100)->value('id');
            if ($keepThreshold) {
                AiChat::where('user_id', $userId)->where('id', '<=', $keepThreshold)->delete();
            }

            DB::commit();

            return response()->json([
                'reply'            => $reply,
                'confidence'       => $processed['confidence'] ?? 'medium',
                'confidence_label' => $processed['confidence_label'] ?? '🟡 Medium Confidence',
                'sources'          => $processed['sources'] ?? [],
                'ai_mode'          => $this->gemini->isAvailable() ? 'gemini' : 'heuristic',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('FinanceAI Engine Error: ' . $e->getMessage());

            return response()->json([
                'reply'            => "**System Warning:** I encountered a processing error. Please try again in a moment.\n\n*Error has been logged for investigation.*",
                'confidence'       => 'low',
                'confidence_label' => '🔴 System Error',
                'sources'          => [],
            ], 500);
        }
    }

    /**
     * MARKET SNAPSHOT ENDPOINT (AJAX — Pro+ only)
     */
    public function marketSnapshot(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        if (!$user->hasPlan('pro')) {
            return response()->json([
                'available' => false,
                'error'     => 'Market data requires Pro plan',
            ]);
        }

        try {
            return response()->json($this->market->getSidebarData());
        } catch (\Throwable $e) {
            return response()->json(['available' => false, 'error' => 'Data unavailable']);
        }
    }

    /**
     * STORE/UPDATE FINANCIAL GOAL (AJAX)
     */
    public function storeGoal(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'title'         => 'required|string|max:100',
            'category'      => 'required|string|in:emergency_fund,house,car,education,retirement,travel,wedding,gadget,custom',
            'target_amount' => 'required|numeric|min:100',
            'target_date'   => 'nullable|date|after:today',
            'priority'      => 'nullable|string|in:high,medium,low',
        ]);

        $goal = FinancialGoal::create([
            'user_id'       => Auth::id(),
            'title'         => $request->title,
            'category'      => $request->category,
            'target_amount' => $request->target_amount,
            'target_date'   => $request->target_date,
            'priority'      => $request->priority ?? 'medium',
        ]);

        return response()->json([
            'success' => true,
            'goal'    => $goal,
            'message' => "Goal '{$goal->title}' created successfully!",
        ]);
    }

    /**
     * SAVE/UPDATE FINANCIAL PROFILE (AJAX)
     */
    public function saveProfile(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'risk_tolerance'        => 'required|in:conservative,moderate,aggressive',
            'investment_experience' => 'required|in:none,beginner,intermediate,advanced',
            'age_group'             => 'required|in:18-25,26-35,36-45,46-55,55+',
            'monthly_income_estimate' => 'nullable|numeric|min:0',
        ]);

        $profile = UserFinancialProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->only(['risk_tolerance', 'investment_experience', 'age_group', 'monthly_income_estimate'])
        );

        return response()->json([
            'success' => true,
            'profile' => $profile,
            'message' => 'Financial profile updated successfully!',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FALLBACK ENGINE (Works without Gemini API key)
    |--------------------------------------------------------------------------
    | Enhanced version of the original rule-based system.
    | Used when: 1) No API key set, 2) Gemini API is down
    */

    private function fallbackEngine(int $userId, string $input, array $intentData): string
    {
        $inputLower = strtolower($input);
        $user = Auth::user();
        $snapshot = $user->financial_snapshot;
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        switch ($intentData['intent']) {

            case 'greeting':
                return $this->generateWelcomeMessage();

            case 'expense_analysis':
                $healthStatus = $snapshot['saving_rate'] >= 20 ? "✅ **Optimal**" : "⚠️ **Needs Optimization**";

                $table = "Here is your **Live Financial Telemetry** for the trailing 30 days:\n\n" .
                    "| Metric | Amount | Status |\n|---|---|---|\n" .
                    "| **Inflow** | ₹" . number_format($snapshot['monthly_income'], 2) . " | 📈 |\n" .
                    "| **Outflow** | ₹" . number_format($snapshot['monthly_expense'], 2) . " | 📉 |\n" .
                    "| **Net Margin** | ₹" . number_format($snapshot['monthly_income'] - $snapshot['monthly_expense'], 2) . " | {$healthStatus} |\n\n" .
                    "**Global Net Asset Value:** ₹" . number_format($snapshot['net_worth'], 2);

                if (!empty($snapshot['top_categories'])) {
                    $table .= "\n\n**Top Expense Categories (30d):**\n";
                    foreach ($snapshot['top_categories'] as $cat => $amount) {
                        $table .= "- **" . ucfirst($cat) . "**: ₹" . number_format((float)$amount, 2) . "\n";
                    }
                }

                return $table;

            case 'savings_strategy':
                $idealSaving = $snapshot['monthly_income'] * 0.20;
                $currentSaving = $snapshot['monthly_income'] - $snapshot['monthly_expense'];
                $gap = $idealSaving - $currentSaving;

                $response = "**Rolling 30-Day Budget Strategy**\n\n";
                $response .= "Standard models suggest retaining **20%** of inflow. Your target: **₹" . number_format($idealSaving, 2) . "**\n\n";

                if ($currentSaving >= $idealSaving) {
                    $response .= "🚀 **Excellent!** You're exceeding your savings target by **₹" . number_format($currentSaving - $idealSaving, 2) . "**.\n\n";
                    $response .= "Consider allocating surplus into:\n";
                    $response .= "- **Index Funds (NIFTY 50)** — Long-term growth\n";
                    $response .= "- **PPF/NPS** — Tax-efficient retirement savings\n";
                    $response .= "- **Liquid Funds** — Emergency buffer";
                } else {
                    $response .= "⚠️ **Gap: ₹" . number_format(abs($gap), 2) . "** short of the 20% benchmark.\n\n";
                    $response .= "**Recommendations:**\n";
                    if (!empty($snapshot['top_categories'])) {
                        $topCat = array_key_first($snapshot['top_categories']);
                        $topAmt = reset($snapshot['top_categories']);
                        $response .= "- Audit **" . ucfirst($topCat) . "** (₹" . number_format((float)$topAmt, 2) . "/mo) — your #1 spend\n";
                    }
                    $response .= "- Review recurring subscriptions\n";
                    $response .= "- Set up auto-transfer to savings account";
                }
                return $response;

            case 'risk_assessment':
                $idealEmergency = $snapshot['monthly_expense'] * 6;

                if ($snapshot['monthly_expense'] > $snapshot['monthly_income'] && $snapshot['monthly_income'] > 0) {
                    return "🚨 **CRITICAL ALERT:** Your 30-day outflows exceed inflows. This burn rate is unsustainable. Immediate budget restructuring required.";
                }

                return "**Risk Assessment & Runway Model**\n\n" .
                    "| Metric | Value |\n|---|---|\n" .
                    "| **30-Day Burn Rate** | ₹" . number_format($snapshot['monthly_expense'], 2) . " |\n" .
                    "| **Financial Runway** | {$snapshot['runway_months']} months |\n" .
                    "| **Emergency Reserve Target (6 Mo)** | ₹" . number_format($idealEmergency, 2) . " |\n" .
                    "| **Current Net Worth** | ₹" . number_format($snapshot['net_worth'], 2) . " |\n" .
                    "| **Savings Rate** | {$snapshot['saving_rate']}% |\n\n" .
                    ($snapshot['runway_months'] < 3
                        ? "⚠️ **Warning:** Your runway is below the recommended 3-month minimum. Build your emergency fund before investing."
                        : "✅ Your runway is healthy. Maintain at least 3-6 months in a high-yield savings account.");

            case 'investment_advice':
                $profile = $user->financialProfile;
                $allocation = $profile
                    ? $profile->getInvestmentAllocation()
                    : (new UserFinancialProfile(['risk_tolerance' => 'moderate']))->getInvestmentAllocation();

                // Extract amount if mentioned
                $amount = 10000;
                if (preg_match('/₹?\s*([\d,]+)/u', $input, $matches)) {
                    $amount = (float) str_replace(',', '', $matches[1]);
                }

                return "**Investment Allocation for ₹" . number_format($amount) . "**\n\n" .
                    "Based on your profile: **{$allocation['label']}**\n\n" .
                    "| Asset Class | Allocation | Amount |\n|---|---|---|\n" .
                    "| **Equity (NIFTY Index Funds)** | {$allocation['equity']}% | ₹" . number_format($amount * $allocation['equity'] / 100) . " |\n" .
                    "| **Debt (PPF / Debt MFs)** | {$allocation['debt']}% | ₹" . number_format($amount * $allocation['debt'] / 100) . " |\n" .
                    "| **Gold (Gold ETF / SGBs)** | {$allocation['gold']}% | ₹" . number_format($amount * $allocation['gold'] / 100) . " |\n" .
                    "| **Liquid (Savings / Liquid MF)** | {$allocation['liquid']}% | ₹" . number_format($amount * $allocation['liquid'] / 100) . " |\n\n" .
                    "**Key Considerations:**\n" .
                    "- Your emergency runway: **{$snapshot['runway_months']} months**\n" .
                    "- Monthly savings capacity: **₹" . number_format(max(0, $snapshot['monthly_income'] - $snapshot['monthly_expense'])) . "**\n\n" .
                    ($snapshot['runway_months'] < 3
                        ? "⚠️ **Priority:** Build emergency fund (3-6 months) before investing in equity.\n\n"
                        : "") .
                    config('financeai.disclaimer');

            case 'market_query':
                if (!$user->hasPlan('pro')) {
                    return "**🔒 Market Data — Pro Feature**\n\nLive market data (crypto, stocks, gold) is available on the **Pro Advisor** plan.\n\nUpgrade to get:\n- Real-time BTC, ETH, SOL prices\n- Gold & commodity tracking\n- AI-powered investment insights\n\nVisit your [Subscription page](/user/subscription) to upgrade.";
                }
                return "**📊 Fetching live market data...**\n\nUse the sidebar Market Pulse widget for real-time prices, or ask me specific questions like:\n- *\"What's the price of Bitcoin?\"*\n- *\"Should I invest in ETH right now?\"*";

            case 'goal_planning':
                $goals = $user->financialGoals()->active()->get();
                if ($goals->isEmpty()) {
                    return "**🎯 Financial Goal Planning**\n\nYou don't have any active goals yet. Setting goals helps track your progress!\n\n**Suggested goals:**\n- 🏦 Emergency Fund (6 months of expenses)\n- 🏠 Home Down Payment\n- 🎓 Education Fund\n- 🚗 Vehicle Purchase\n\nClick the **\"Set Goal\"** button in the sidebar to create your first goal.";
                }

                $table = "**🎯 Your Active Financial Goals**\n\n| Goal | Progress | Target | Monthly Needed |\n|---|---|---|---|\n";
                foreach ($goals as $g) {
                    $status = $g->isOnTrack() ? '✅' : '⚠️';
                    $table .= "| {$status} {$g->title} | {$g->progress_percent}% | ₹" . number_format($g->target_amount) . " | ₹" . number_format($g->monthly_required ?? 0) . " |\n";
                }
                return $table;

            case 'profile_setup':
                return "**📋 Financial Profile Setup**\n\nTo give you personalized recommendations, I need to know:\n\n1. **Risk Tolerance**: Conservative / Moderate / Aggressive\n2. **Investment Experience**: None / Beginner / Intermediate / Advanced\n3. **Age Group**: 18-25 / 26-35 / 36-45 / 46-55 / 55+\n\nClick the **profile setup widget** in the sidebar, or tell me directly:\n*\"I'm 28, moderate risk, beginner investor\"*";

            default:
                return "I can help you with:\n\n" .
                    "- 📊 `/analyze` — Full financial report\n" .
                    "- 💰 `/invest 10000` — Investment allocation\n" .
                    "- 📈 `/market` — Live market prices (Pro)\n" .
                    "- 🎯 `/goals` — Financial goals tracker\n" .
                    "- 🛡️ `/runway` — Risk assessment\n" .
                    "- 📋 `/profile` — Set up your financial profile\n\n" .
                    "Or just ask me anything about your finances!";
        }
    }

    private function generateWelcomeMessage(): string
    {
        $user = Auth::user();
        $firstName = explode(' ', trim($user->name ?? 'User'))[0];
        $snapshot = $user->financial_snapshot;

        $aiMode = $this->gemini->isAvailable() ? 'Gemini AI' : 'Heuristic Engine';
        $health = $this->getHealthLabel($snapshot['saving_rate']);

        return "👋 **Welcome to FinanceAI, {$firstName}.**\n\n" .
               "I've synchronized with your financial ledger. Here's your quick snapshot:\n\n" .
               "| Metric | Value |\n|---|---|\n" .
               "| **Net Worth** | ₹" . number_format($snapshot['net_worth'], 2) . " |\n" .
               "| **Monthly Savings** | ₹" . number_format(max(0, $snapshot['monthly_income'] - $snapshot['monthly_expense']), 2) . " |\n" .
               "| **Savings Rate** | {$snapshot['saving_rate']}% |\n" .
               "| **Financial Health** | {$health} |\n\n" .
               "**AI Engine**: {$aiMode} · **Plan**: {$user->current_plan_name}\n\n" .
               "Try:\n" .
               "- *\"Analyze my expenses\"*\n" .
               "- *\"Where should I invest ₹10,000?\"*\n" .
               "- *\"/market\"* for live prices (Pro)\n" .
               "- *\"/goals\"* to track financial goals";
    }

    private function getHealthLabel(float $savingRate): string
    {
        return match (true) {
            $savingRate >= 30 => '🟢 Excellent',
            $savingRate >= 20 => '🟢 Healthy',
            $savingRate >= 10 => '🟡 Needs Improvement',
            $savingRate >= 0  => '🔴 Critical',
            default           => '🔴 Deficit',
        };
    }
}