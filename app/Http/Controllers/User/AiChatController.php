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
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AiChatController extends Controller
{
    /**
     * SHOW AI CHAT INTERFACE
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();

        // Fetch last 50 messages for context window
        $chats = AiChat::where('user_id', $userId)
            ->latest('id')
            ->take(50)
            ->get()
            ->reverse();

        // Initialize Context for First Time User
        if ($chats->isEmpty()) {
            $welcomeMessage = $this->generateWelcomeMessage();
            
            // Generate a daily session ID for conversational grouping
            $sessionId = 'chat_' . now()->format('Ymd');

            $chat = AiChat::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'sender'     => AiChat::SENDER_AI,
                'message'    => $welcomeMessage,
                'tokens'     => intval(strlen($welcomeMessage) / 4), // Simulate LLM token usage
            ]);

            $chats = collect([$chat]);
        }

        return view('user.ai-chat', compact('chats'));
    }

    /**
     * HANDLE USER MESSAGE (AJAX)
     */
    public function sendMessage(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'reply' => 'Session expired. Please login again.'
            ], 401);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId    = Auth::id();
        $message   = trim($request->message);
        $sessionId = 'chat_' . now()->format('Ymd');

        try {
            DB::beginTransaction();

            // 1. Save USER message securely using Strict Model Constants
            AiChat::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'sender'     => AiChat::SENDER_USER,
                'message'    => $message,
                'tokens'     => intval(strlen($message) / 4), 
            ]);

            // 2. Process Input via Internal Financial Engine
            $reply = $this->processFinancialQuery($userId, $message);

            // 3. Save AI Response
            AiChat::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'sender'     => AiChat::SENDER_AI,
                'message'    => $reply,
                'tokens'     => intval(strlen($reply) / 4), 
            ]);

            // 4. 🔥 BEAST MODE: High-Performance Database Pruning 
            // Keeps the table lean without using heavy memory arrays
            $keepThreshold = AiChat::where('user_id', $userId)->latest('id')->skip(100)->value('id');
            if ($keepThreshold) {
                AiChat::where('user_id', $userId)->where('id', '<=', $keepThreshold)->delete();
            }

            DB::commit();

            return response()->json([
                'reply' => $reply,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('FinanceAI Engine Error: ' . $e->getMessage());

            return response()->json([
                'reply' => "**System Warning:** I encountered a secure connection error while analyzing your ledger. Please try again in a moment."
            ], 500);
        }
    }

    /**
     * MAIN AI RULE-BASED ENGINE
     * Analyzes user intent and queries financial data to formulate a Markdown response.
     */
    private function processFinancialQuery(int $userId, string $input): string
    {
        $input = strtolower($input);
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // 1. Aggregate Core Financial Metrics
        $totalIncome  = (float) (Income::where('user_id', $userId)->sum('amount') ?? 0);
        $totalExpense = (float) (Expense::where('user_id', $userId)->sum('amount') ?? 0);
        $netWorth     = $totalIncome - $totalExpense;
        $savingRate   = $totalIncome > 0 ? round(($netWorth / $totalIncome) * 100, 1) : 0;

        // 2. Time-Based Metrics (Rolling 30 Days)
        $monthlyIncome = (float) (Income::where('user_id', $userId)
                                ->where('created_at', '>=', $thirtyDaysAgo)
                                ->sum('amount') ?? 0);
                                
        $monthlyBurn   = (float) (Expense::where('user_id', $userId)
                                ->where('created_at', '>=', $thirtyDaysAgo)
                                ->sum('amount') ?? 0);
        
        $runway = $monthlyBurn > 0 ? round($netWorth / $monthlyBurn, 1) : '> 12';

        /* ========= 📊 SLASH COMMAND: /analyze & OVERVIEW ========= */
        if ($input === '/analyze' || Str::contains($input, ['analyze', 'analysis', 'overview', 'summary', 'report'])) {
            $healthStatus = $savingRate >= 20 ? "✅ **Optimal**" : "⚠️ **Needs Optimization**";
            
            return "Here is your **Live Financial Telemetry** for the trailing 30 days:\n\n" .
                   "| Metric | Amount | Status |\n" .
                   "|---|---|---|\n" .
                   "| **Inflow** | ₹" . number_format($monthlyIncome, 2) . " | 📈 |\n" .
                   "| **Outflow** | ₹" . number_format($monthlyBurn, 2) . " | 📉 |\n" .
                   "| **Net Margin** | ₹" . number_format($monthlyIncome - $monthlyBurn, 2) . " | {$healthStatus} |\n\n" .
                   "**Global Net Asset Value:** ₹" . number_format($netWorth, 2) . "\n\n" .
                   "Based on current data models, I recommend maintaining a minimum of 3 months' runway in highly liquid assets.";
        }

        /* ========= 🛑 TOP EXPENSES & CATEGORIES ========= */
        if (Str::contains($input, ['biggest', 'top', 'highest expense', 'category', 'categories'])) {
            // Dynamically fetch the actual highest burn category
            $topCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
                ->where('user_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->groupBy('category')
                ->orderByDesc('total')
                ->first();

            if (!$topCategory) {
                return "I cannot identify a top expense category because your operational ledger is currently empty for the trailing 30 days.";
            }

            return "Your highest capital drain over the trailing 30 days is **" . ucfirst($topCategory->category) . "**, totaling **₹" . number_format((float)$topCategory->total, 2) . "**.\n\n" .
                   "Here is the database query I executed to retrieve this anomaly:\n\n" .
                   "```sql\n" .
                   "SELECT category, SUM(amount) as Total \n" .
                   "FROM expenses \n" .
                   "WHERE user_id = {$userId} AND created_at >= NOW() - INTERVAL 30 DAY \n" .
                   "GROUP BY category \n" .
                   "ORDER BY Total DESC LIMIT 1;\n" .
                   "```\n\n" .
                   "I recommend executing a strict audit on this specific vector.";
        }

        /* ========= 💰 SAVINGS & BUDGETING ========= */
        if (Str::contains($input, ['save', 'saving', 'budget', 'cut', 'reduce'])) {
            $idealSaving = $monthlyIncome * 0.20;
            $currentMonthSavings = $monthlyIncome - $monthlyBurn;
            $gap = $idealSaving - $currentMonthSavings;

            $response = "Let's review your **Rolling 30-Day Budget Strategy**.\n\n";
            $response .= "Standard algorithmic models suggest retaining 20% of your operational inflow. Your monthly target is **₹" . number_format($idealSaving, 2) . "**.\n\n";

            if ($currentMonthSavings >= $idealSaving) {
                $response .= "🚀 **Excellent Discipline!** You are exceeding your retention target. Consider allocating excess capital into equity index funds.";
            } else {
                $response .= "⚠️ **Optimization Required.** You are currently **₹" . number_format(abs($gap), 2) . "** short of the 20% benchmark this month. I recommend a strict audit of recurring subscriptions and variable utility outflows.";
            }
            return $response;
        }

        /* ========= 🛫 SLASH COMMAND: /runway & RISK ========= */
        if ($input === '/runway' || Str::contains($input, ['risk', 'emergency', 'runway', 'danger', 'safe'])) {
            $idealEmergency = $monthlyBurn * 6; 

            if ($monthlyBurn > $monthlyIncome && $monthlyIncome > 0) {
                return "🚨 **CRITICAL ALERT:** Your 30-day trailing outflows exceed your inflows. This operational burn rate is unsustainable. Immediate budget restructuring is mandated.";
            }

            return "Here is your **Risk Assessment & Runway Model**:\n\n" .
                   "- **Current 30-Day Burn Rate:** ₹" . number_format($monthlyBurn, 2) . "\n" .
                   "- **Projected Runway:** {$runway} months\n" .
                   "- **Target Emergency Reserve (6 Mo):** ₹" . number_format($idealEmergency, 2) . "\n\n" .
                   "Maintain at least 3-6 months of runway in a high-yield savings account to insulate your node against market volatility.";
        }

        /* ========= 📈 INVESTMENTS ========= */
        if (Str::contains($input, ['invest', 'grow', 'portfolio', 'equity', 'stock', 'mutual fund'])) {
            return "Based on standard capital allocation models, here is a structured **Investment Strategy** for your surplus liquidity:\n\n" .
                   "- **50% Growth (Equity/Index Funds):** High potential, long-term horizon.\n" .
                   "- **30% Stability (Debt/Bonds):** Capital preservation and inflation hedging.\n" .
                   "- **20% Liquid (Emergency/Cash):** Immediate operational access.\n\n" .
                   "*Disclaimer: I am an algorithmic analytics engine. Please consult a registered human financial advisor before executing live market trades.*";
        }

        /* ========= 🎓 PRESENTATION EASTER EGGS ========= */
        if (Str::contains($input, ['ahmedabad', 'gtu', 'bca', 'project', 'evaluate'])) {
            return "👨‍💻 **System Architecture: Master Node Identified**\n\n" .
                   "FinanceAI is a high-performance, enterprise-grade application compiled specifically for the Semester 6 Evaluation at **Gujarat Technological University (GTU)**.\n\n" .
                   "Engineered with a strict MVC architecture, cryptographic token management, and mathematical data visualization to deliver unparalleled financial telemetry.";
        }

        if (Str::contains($input, ['bgmi', 'uc', 'game', 'gaming'])) {
            return "🎮 **Recreational Analytics:**\n\n" .
                   "While recreational digital entertainment is acceptable, algorithmic analysis dictates that digital micro-transactions (such as BGMI UC purchases) should never exceed **5%** of your total monthly discretionary allowance.";
        }

        if (Str::contains($input, ['crazy chat corner', 'food', 'stall', 'dining', 'eat'])) {
            return "🍔 **Vendor Intelligence:**\n\n" .
                   "When auditing your `Dining & Entertainment` ledger, ensure local vendors and quick-service establishments (e.g., Crazy Chat Corner) do not consume more than **15%** of your monthly operational capital to maintain optimal savings velocity.";
        }

        /* ========= 👋 GREETINGS ========= */
        if (Str::contains($input, ['hi', 'hello', 'hey', 'morning', 'evening', 'help'])) {
            return $this->generateWelcomeMessage();
        }

        /* ========= 🤖 DEFAULT CATCH-ALL ========= */
        return "I am currently analyzing your input.\n\nAs your Financial AI Engine, I can execute the following operations:\n" .
               "- `/analyze` (Generate a 30-day report)\n" .
               "- `/runway` (Calculate your survival months)\n" .
               "- `What is my biggest expense category?`\n" .
               "- `Define an investment strategy`\n\n" .
               "How would you like to proceed?";
    }

    private function generateWelcomeMessage(): string
    {
        $userName = Auth::user()->name ?? 'Operator';
        $firstName = explode(' ', trim($userName))[0];

        return "👋 **Welcome to the FinanceAI Core, {$firstName}.**\n\n" .
               "I have securely synchronized with your data ledger. I am standing by to analyze your cashflow, predict burn rates, and identify structural anomalies.\n\n" .
               "Try issuing a command like:\n" .
               "- *\"Analyze my monthly expenses\"*\n" .
               "- *\"What is my top expense category?\"*\n" .
               "- *\"Check my emergency fund runway\"*";
    }
}