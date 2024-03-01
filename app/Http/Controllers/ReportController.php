<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Wallet;
use App\Models\Earning;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Exports\DepositExport;
use App\Models\InvestmentPlan;
use App\Exports\WithdrawalExport;
use App\Exports\ReturnReportExport;
use App\Exports\EarningReportExport;
use App\Models\SettingWalletAddress;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\InvestmentSubscription;
use App\Exports\InvestmentReportExport;
use function Symfony\Component\Translation\t;

class ReportController extends Controller
{
    public function detail()
    {
        $standardRewards = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'StandardRewards')
            ->where('category', 'standard')
            ->sum('after_amount');

        $standardReferralEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'ReferralEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $affiliateEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'AffiliateEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $dividendEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'DividendEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $affiliateDividendEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'AffiliateDividendEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $stakingRewards = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'StakingRewards')
            ->where('category', 'staking')
            ->sum('after_coin_price');

        $stakingReferralEarnings= Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'ReferralEarnings')
            ->where('category', 'staking')
            ->sum('after_coin_price');

        $pairingEarnings= Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'PairingEarnings')
            ->where('category', 'staking')
            ->sum('after_coin_price');

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
            'investmentPlans' => $translatedInvestmentPlans,
            'standardRewards' => $standardRewards,
            'standardReferralEarnings' => $standardReferralEarnings,
            'affiliateEarnings' => $affiliateEarnings,
            'dividendEarnings' => $dividendEarnings,
            'affiliateDividendEarnings' => $affiliateDividendEarnings,
            'stakingRewards' => $stakingRewards,
            'stakingReferralEarnings' => $stakingReferralEarnings,
            'pairingEarnings' => $pairingEarnings,
        ]);
    }

    public function getReturnRecord(Request $request)
    {
        $user = \Auth::user();

        $query = Earning::query()
            ->with(['subscriptionPlan.investment_plan', 'wallet:id,name,type', 'coin.setting_coin', 'coinStacking.investment_plan'])
            ->where('upline_id', $user->id)
            ->whereIn('type', ['StandardRewards', 'StakingRewards', 'DividendEarnings']);

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
            $query->where('type', $type);
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            $query->whereBetween('roi_release_date', [$start_date, $end_date]);
        }

        if ($request->has('exportStatus')) {
            return Excel::download(new ReturnReportExport($query), Carbon::now() . '-return-report.xlsx');
        }

        $results = $query->latest()->paginate(10);

        return response()->json($results);
    }

    public function getEarningRecord(Request $request)
    {
        $user = \Auth::user();

        $query = Earning::query()
            ->with(['downline:id,name,email', 'wallet:id,name,type', 'coin.setting_coin'])
            ->where('upline_id', $user->id)
            ->whereIn('type', ['AffiliateEarnings', 'ReferralEarnings', 'AffiliateDividendEarnings', 'PairingEarnings']);

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

        $investmentSubscriptions = DB::table('users')
            ->select(
                'investment_subscriptions.id as investment_subscription_id',
                'investment_subscriptions.investment_plan_id as investment_plan_id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(investment_plans.name, '$.en')) as plan_name"),
                'investment_plans.type as plan_type',
                'investment_subscriptions.amount as subscription_amount',
                'investment_subscriptions.total_earning as subscription_unit',
                'investment_subscriptions.subscription_id as subscription_id',
                'investment_subscriptions.status as subscription_status',
                'investment_subscriptions.expired_date as subscription_expired_date',
                'investment_subscriptions.created_at as subscription_date'
            )
            ->leftJoin('investment_subscriptions', 'users.id', '=', 'investment_subscriptions.user_id')
            ->leftJoin('investment_plans', 'investment_subscriptions.investment_plan_id', '=', 'investment_plans.id')
            ->where('users.id', $user->id);

        $coinStakings = DB::table('users')
            ->select(
                'coin_stackings.id as coin_stacking_id',
                'coin_stackings.investment_plan_id as investment_plan_id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(investment_plans.name, '$.en')) as plan_name"),
                'investment_plans.type as plan_type',
                'coin_stackings.stacking_price as subscription_amount',
                'coin_stackings.stacking_unit as subscription_unit',
                'coin_stackings.subscription_number as subscription_id',
                'coin_stackings.status as subscription_status',
                'coin_stackings.expired_date as subscription_expired_date',
                'coin_stackings.created_at as subscription_date'
            )
            ->leftJoin('coin_stackings', 'users.id', '=', 'coin_stackings.user_id')
            ->leftJoin('investment_plans', 'coin_stackings.investment_plan_id', '=', 'investment_plans.id')
            ->where('users.id', $user->id);

        // Search condition
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';

            $investmentSubscriptions->where('investment_subscriptions.subscription_id', 'like', $search);
            $coinStakings->where('coin_stackings.subscription_number', 'like', $search);
        }

        // Check for the date condition
        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            // Apply date range condition to each query
            $investmentSubscriptions->whereBetween('investment_subscriptions.created_at', [$start_date, $end_date]);
            $coinStakings->whereBetween('coin_stackings.created_at', [$start_date, $end_date]);
        }

        // Check for the type condition
        if ($request->filled('type')) {
            $type = $request->input('type');
            $investmentSubscriptions->where('investment_subscriptions.status', $type);
            $coinStakings->where('coin_stackings.status', $type);
        }

        // Union the results
        $combinedResults = $investmentSubscriptions->union($coinStakings);

        // Apply orderBy
        $combinedResults = $combinedResults->orderByDesc('subscription_date')->paginate(10);

        return response()->json($combinedResults);
//        $user = \Auth::user();
//
//        $query = InvestmentSubscription::query()
//            ->with('investment_plan:id,name,roi_per_annum,investment_period')
//            ->where('user_id', $user->id);
//
//        if ($request->filled('search')) {
//            $search = '%' . $request->input('search') . '%';
//            $query->where(function ($q) use ($search) {
//                $q->whereHas('investment_plan', function ($investment_plan) use ($search) {
//                    $investment_plan->where('name', 'like', $search);
//                })
//                    ->orWhere('subscription_id', 'like', $search);
//            });
//        }
//
//        if ($request->filled('type')) {
//            $type = $request->input('type');
//            $query->where('status', $type);
//        }
//
//        if ($request->filled('date')) {
//            $date = $request->input('date');
//            $dateRange = explode(' - ', $date);
//            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
//            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();
//
//            $query->whereBetween('created_at', [$start_date, $end_date]);
//        }
//
//        if ($request->has('exportStatus')) {
//            return Excel::download(new InvestmentReportExport($query), Carbon::now() . '-investment-report.xlsx');
//        }
//
//        $results = $query->latest()->paginate(10);
//
//        return response()->json($results);
    }
}
