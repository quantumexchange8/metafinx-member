<?php

namespace App\Http\Controllers;

use App\Exports\DepositExport;
use App\Exports\EarningReportExport;
use App\Exports\InvestmentReportExport;
use App\Exports\WithdrawalExport;
use App\Models\Earning;
use App\Models\InvestmentPlan;
use App\Models\InvestmentSubscription;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\SettingWalletAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use function Symfony\Component\Translation\t;

class ReportController extends Controller
{
    public function detail()
    {
        $totalEarning = Earning::where('upline_id', \Auth::id())
            ->sum('after_amount');

        $totalWithdrawal = Payment::where('user_id', \Auth::id())
            ->where('type', '=', 'Withdrawal')
            ->where('status', '=', 'Success')
            ->sum('amount');

        $totalInvestment = InvestmentSubscription::where('user_id', \Auth::id())
            ->sum('amount');

        $totalBalance = Wallet::where('user_id', \Auth::id())
            ->sum('balance');

        $investment_plans = InvestmentPlan::query()
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        $translatedInvestmentPlans = $investment_plans->map(function ($investmentPlan) {
            return [
                'id' => $investmentPlan->id,
                'name' => $investmentPlan->getTranslation('name', app()->getLocale()),
            ];
        });
        $translatedInvestmentPlans->prepend(['id' => '', 'name' => 'All']);

        return Inertia::render('Report/Report', [
            'totalEarning' => floatval($totalEarning),
            'totalWithdrawal' => floatval($totalWithdrawal),
            'totalInvestment' => floatval($totalInvestment),
            'totalBalance' => floatval($totalBalance),
            'investmentPlans' => $translatedInvestmentPlans,
        ]);
    }

    public function getReturnRecord(Request $request)
    {
        $user = \Auth::user();

        $query = Earning::query()
            ->with('subscriptionPlan.investment_plan')
            ->where('type', 'monthly_return');

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('subscriptionPlan', function ($subPlan) use ($search) {
                    $subPlan->where('subscription_id', 'like', $search);
                });
            });
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->whereHas('subscriptionPlan.investment_plan', function ($plan) use ($type) {
                $plan->where('id', $type);
            });
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            $query->whereBetween('roi_release_date', [$start_date, $end_date]);
        }

        if ($request->has('exportStatus')) {
            return Excel::download(new EarningReportExport($query), Carbon::now() . '-earning-report.xlsx');
        }

        $results = $query->latest()->paginate(10);

        return response()->json($results);
    }

    public function getEarningRecord(Request $request)
    {
        $user = \Auth::user();

        $query = Earning::query()
            ->with(['downline:id,name,email', 'wallet:id,name,type'])
            ->where('upline_id', $user->id);

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('downline', function ($downline) use ($search) {
                    $downline->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
            });
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->where('type', $type);
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            $query->whereBetween('created_at', [$start_date, $end_date]);
        }

        if ($request->has('exportStatus')) {
            return Excel::download(new EarningReportExport($query), Carbon::now() . '-earning-report.xlsx');
        }

        $results = $query->latest()->paginate(10);

        $results->each(function ($user_deposit) {
            $user_deposit->downline->profile_photo_url = $user_deposit->downline->getFirstMediaUrl('profile_photo');
        });

        return response()->json($results);
    }

    public function getInvestmentRecord(Request $request)
    {
        $user = \Auth::user();

        $query = InvestmentSubscription::query()
            ->with('investment_plan:id,name,roi_per_annum,investment_period')
            ->where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('investment_plan', function ($investment_plan) use ($search) {
                    $investment_plan->where('name', 'like', $search);
                })
                    ->orWhere('subscription_id', 'like', $search);
            });
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->where('status', $type);
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            $query->whereBetween('created_at', [$start_date, $end_date]);
        }

        if ($request->has('exportStatus')) {
            return Excel::download(new InvestmentReportExport($query), Carbon::now() . '-investment-report.xlsx');
        }

        $results = $query->latest()->paginate(10);

        return response()->json($results);
    }
}
