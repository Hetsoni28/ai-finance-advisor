<style>
    @page { margin: 18mm 15mm 20mm 15mm; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #1e293b; line-height: 1.45; margin: 0; }

    .footer { position: fixed; bottom: -12mm; left: 0; right: 0; height: 8mm; border-top: 1px solid #e2e8f0; padding-top: 2mm; font-size: 8px; color: #94a3b8; }
    .footer .right { float: right; }
    .footer .pg:after { content: counter(page); }

    .brand { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 8px; margin-bottom: 14px; }
    .brand td { vertical-align: bottom; }
    .logo { font-size: 17px; font-weight: bold; color: #1e3a8a; }
    .logo span { color: #3b82f6; }
    .doc-title { font-size: 15px; font-weight: bold; color: #0f172a; text-align: right; }
    .doc-sub { font-size: 8.5px; color: #64748b; text-align: right; }

    .meta { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
    .meta td { padding: 3px 0; font-size: 9px; vertical-align: top; }
    .meta .k { color: #64748b; width: 18%; }
    .meta .v { color: #0f172a; font-weight: bold; width: 32%; }

    .cards { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px 16px -6px; }
    .card { border: 1px solid #dbeafe; background: #eff6ff; border-radius: 4px; padding: 8px 10px; width: 25%; }
    .card .k { font-size: 8px; color: #475569; text-transform: uppercase; letter-spacing: .4px; }
    .card .v { font-size: 13.5px; font-weight: bold; color: #0f172a; margin-top: 3px; }
    .card .n { font-size: 8px; color: #64748b; margin-top: 2px; }

    h2 { font-size: 11.5px; color: #1e3a8a; margin: 16px 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #e2e8f0; }
    .note { font-size: 8.5px; color: #64748b; margin: -2px 0 6px 0; }

    table.data { width: 100%; border-collapse: collapse; }
    table.data th { background: #1e3a8a; color: #fff; font-size: 8.5px; font-weight: bold; text-align: left; padding: 5px 6px; }
    table.data td { padding: 4.5px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9px; vertical-align: middle; }
    table.data tr.alt td { background: #f8fafc; }
    table.data tr.sub td { background: #eff6ff; font-weight: bold; color: #1e3a8a; border-bottom: 1px solid #bfdbfe; }
    table.data tr.total td { background: #1e3a8a; color: #fff; font-weight: bold; }
    table.data thead { display: table-header-group; }
    table.data tr { page-break-inside: avoid; }
    table.data tr.sub { page-break-after: avoid; }
    .r, table.data th.r, table.data td.r { text-align: right; }
    .c { text-align: center; }
    .muted { color: #64748b; }
    .neg { color: #b91c1c; }
    .pos { color: #15803d; }

    .bar { height: 7px; background: #e2e8f0; border-radius: 3px; width: 100%; }
    .bar div { height: 7px; background: #3b82f6; border-radius: 3px; }
    .bar.green div { background: #16a34a; }
    .bar.red div { background: #dc2626; }

    .pill { font-size: 7.5px; padding: 1px 6px; border-radius: 8px; background: #e0e7ff; color: #3730a3; }
    .pill.green { background: #dcfce7; color: #166534; }
    .pill.red { background: #fee2e2; color: #991b1b; }
    .pill.grey { background: #f1f5f9; color: #475569; }

    .end { margin-top: 14px; padding: 8px 10px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 8.5px; color: #64748b; }
    .keep { page-break-inside: avoid; }
</style>
