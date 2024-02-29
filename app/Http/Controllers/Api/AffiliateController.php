<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\CoinStacking;
use App\Models\CoinMultiLevel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\InvestmentSubscription;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function my_group()
    {
        $user = \Auth::user();

        $descendants = $user->getDescendants();

        // Organize descendants by generations
        $groupedDescendants = $descendants->groupBy(function ($item) {
            return 'Gen ' . ($item->getLevel() - 1);
        })->map(function ($generation) {
            return $generation->map(function ($item) {
                return [
                    'image_address' => $item->getFirstMediaUrl('profile_photo'),
                    'name' => $item->name,
                    'email' => $item->email,
                    'ranking' => $item->setting_rank_id,
                    'upline' => $item->upline ? $item->upline->name : null,
                    'direct_referrals' => count($item->children),
                    'total_affiliates' => count($item->getChildrenIds()),
                    'vsd' => $this->getSelfDeposit($item),
                    'vad' => $this->getValidAffiliateDeposit($item),
                    'internal_wallet_balance' => $item->wallets[0]->balance,
                ];
            });
        });

        return response()->json($groupedDescendants);
    }

    protected function getSelfDeposit($user)
    {
        return InvestmentSubscription::query()
            ->where('user_id', $user->id)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');
    }

    protected function getValidAffiliateDeposit($user)
    {
        $ids = $user->getChildrenIds();

        return InvestmentSubscription::query()
            ->whereIn('user_id', $ids)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');
    }

    public function binary_tree()
    {
        $user = \Auth::user();
        $binaryUser = CoinMultiLevel::with(['user:id,name,email,setting_rank_id', 'sponsor.user', 'children'])->where('user_id', $user->id)->first();
        
        $binaryData = $this->mapBinaryUser($binaryUser, 0);
        
        return response()->json($binaryData);
    }
    
    protected function mapBinaryUser($user, $level)
    {
        $children = $user->children;
    
        $mappedChildren = $children->map(function ($child) use ($level) {
            $mappedChild = $this->mapBinaryUser($child, $level + 1);
            $mappedChild['position'] = $child->position; 
            return $mappedChild;
        });
    
        $current_staking = $this->getCurrentStaking($user);
        $accrued_staking = $this->getAccuredStaking($user);
        $leftAmount = $this->getLeftAmount($user);
        $rightAmount = $this->getRightAmount($user);

        // Calculate left and right carry forward amounts
        $leftCarryForward = $leftAmount >= $rightAmount ? ($leftAmount - $rightAmount) : '0';
        $rightCarryForward = $rightAmount >= $leftAmount ? ($rightAmount - $leftAmount) : '0';
    
        // Separate children into 'left' and 'right' arrays based on their positions
        $leftChildren = null;
        $rightChildren = null;
        foreach ($mappedChildren as $mappedChild) {
            if (isset($mappedChild['position']) && $mappedChild['position'] === 'left') {
                unset($mappedChild['position']); // Remove 'position' attribute from child
                $leftChildren = $mappedChild;
            } elseif (isset($mappedChild['position']) && $mappedChild['position'] === 'right') {
                unset($mappedChild['position']); // Remove 'position' attribute from child
                $rightChildren = $mappedChild;
            }
        }

        $mappedUser = [
            'id' => $user->id,
            'profile_image' => $user->user->getFirstMediaUrl('profile_photo'),
            'name' => $user->user->name,
            'email' => $user->user->email,
            'gen' => $level,
            'current_staking' => $current_staking,
            'accrued_staking' => $accrued_staking,
            'left_carry_forward' => $leftCarryForward,
            'right_carry_forward' => $rightCarryForward,
            'left' => $leftChildren,
            'right' => $rightChildren,
        ];
    
        return $mappedUser;
    }                        
    
    protected function getLeftAmount($child)
    {
        $amount = 0;

        $direct_child = $child->direct_child('left')->first();

        if ($direct_child) {
            $ids = $direct_child->getChildrenIds();

            $binary_user_id = CoinMultiLevel::query()
                ->whereIn('id', $ids)
                ->pluck('user_id')
                ->toArray();

            $amount += CoinStacking::whereIn('user_id', $binary_user_id)
                ->where('status', 'OnGoingPeriod')
                ->sum('stacking_unit');

            $amount += CoinStacking::where('user_id', $direct_child->user_id)
                ->where('status', 'OnGoingPeriod')
                ->sum('stacking_unit');
        }

        return $amount;
    }

    protected function getRightAmount($child)
    {
        $amount = 0;

        $direct_child = $child->direct_child('right')->first();

        if ($direct_child) {
            $ids = $direct_child->getChildrenIds();

            $binary_user_id = CoinMultiLevel::query()
                ->whereIn('id', $ids)
                ->pluck('user_id')
                ->toArray();

            $amount += CoinStacking::whereIn('user_id', $binary_user_id)
                ->where('status', 'OnGoingPeriod')
                ->sum('stacking_unit');

            $amount += CoinStacking::where('user_id', $direct_child->user_id)
                ->where('status', 'OnGoingPeriod')
                ->sum('stacking_unit');
        }

        return $amount;
    }

    protected function getCurrentStaking($child)
    {
        // Calculate current staking
        $currentStakingUnit = CoinStacking::where('user_id', $child->user_id)
            ->where('status', 'OnGoingPeriod')
            ->sum('stacking_unit');
        
        return $currentStakingUnit;
    }

    protected function getAccuredStaking($child)
    {
        // Calculate accrued staking
        $accruedStakingUnit = CoinStacking::where('user_id', $child->user_id)
            ->where('status', 'MaturityPeriod')
            ->sum('stacking_unit');
        
        return $accruedStakingUnit;
    }

    public function getAvailableBinaryAffiliate(Request $request)
    {
        $user = Auth::user();
        $position = $request->position ?? 'left';
        $existed_users_ids = CoinMultiLevel::pluck('user_id')->toArray();
        $childrenIds = $user->children()->pluck('id')->toArray();

        $binaryAuthUser = CoinMultiLevel::where('user_id', $user->id)->first();
        $directChild = $binaryAuthUser->direct_child($position)->first();
    
        $last_child = $directChild ? $directChild->getLastChild($directChild, 'left') : $binaryAuthUser;
        if ($last_child) {
            $binaryData = $this->binary_tree()->getContent();
            $binaryData = json_decode($binaryData, true);
    
            $userIdToFind = $last_child->id;

            $foundUser = $this->findUserById($binaryData, $userIdToFind);
    
            $last_child_user = $last_child->user;
        
            // Transform $last_child directly
            $last_child_user->profile_picture = $last_child_user->getFirstMediaUrl('profile_photo');

            $last_child = [
                'id' => $last_child->id,
                'profile_picture' => $last_child_user->profile_picture,
                'name' => $last_child_user->name,
                'email' => $last_child_user->email,
                'gen' => $foundUser['gen'],
            ];
        }
                
        $users = User::query()
            ->where('role', 'user')
            ->where('auto_assign_at', '>=', now())
            ->whereDate('created_at', '>=', now()->subDay())
            ->whereIn('id', $childrenIds)
            ->whereNotIn('id', $existed_users_ids)
            ->select('id', 'name', 'email', 'created_at', 'auto_assign_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $users->transform(function ($user) use ($last_child) {
            return [
                'id' => $user->id,
                'profile_picture' => $user->getFirstMediaUrl('profile_photo'),
                'name' => $user->name,
                'email' => $user->email,
                'expired_date' => $user->auto_assign_at,
                'staking_amount' => $this->getCurrentStaking($user),
                'place_under' => $last_child,
            ];
        });

        return response()->json($users);
    }

    private function findUserById($dataArray, $userId)
    {
        $queue = [$dataArray]; // Start with the root node
        while (!empty($queue)) {
            $currentNode = array_shift($queue); // Get the next node from the queue
            if ($currentNode['id'] === $userId) {
                return $currentNode; // User found
            }
            // Add left and right children to the queue if they exist
            if (isset($currentNode['left'])) {
                $queue[] = $currentNode['left'];
            }
            if (isset($currentNode['right'])) {
                $queue[] = $currentNode['right'];
            }
        }
        return null; // User not found
    }

    public function getLastChild(Request $request)
    {
        $user = Auth::user();
        $position = $request->position ?? 'left';
            
        $binaryAuthUser = CoinMultiLevel::where('user_id', $user->id)->first();
        $directChild = $binaryAuthUser->direct_child($position)->first();
    
        $last_child = $directChild ? $directChild->getLastChild($directChild, 'left') : $binaryAuthUser;
        if ($last_child) {
            $last_child->profile_photo = $last_child->user->getFirstMediaUrl('profile_photo');
        }
    
        return response()->json($last_child);
    }

    public function addDistributor(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'upline_id' => ['required'],
            'position' => ['required', 'in:left,right'],
            'user_id' => ['required'],
        ])->setAttributeNames([
            'upline_id' => 'Upline',
            'position' => 'Position',
            'user_id' => 'User Id'
        ]);

        if (!$validator->passes()) {
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()->toArray()
            ]);
        } else {

            $upline = CoinMultiLevel::find($request->upline_id);
            $sponsor = CoinMultiLevel::where('user_id', Auth::id())->first();
            $coinStakingPrice = CoinStacking::where('user_id', $request->user_id)->where('status', 'OnGoingPeriod')->sum('stacking_price');

            $directChild = $upline->direct_child($request->position)->first();
            // Ensure the specified position is either 'left' or 'right'
            $position = $request->position;

            // Update the hierarchy list based on the upline
            if ($upline->id == 1) {
                // If the upline is the root node, the hierarchy list will be the user's ID
                $hierarchyList = '-' . $request->upline_id . '-';
            } else {
                // Otherwise, prepend the upline's hierarchy list with a '-' if it's not empty
                $hierarchyList = $upline->hierarchy_list . $upline->id . '-';
            }

            $data = [
                'user_id' => $request->user_id,
                'sponsor_id' => $sponsor->id,
                'upline_id' => $upline->id,
                'hierarchy_list' => $hierarchyList,
                'position' => 'left', // default position
                'coin_stacking_amount' => $coinStakingPrice,
            ];

            if (empty($upline->direct_child('left')->first()) && empty($upline->direct_child('right')->first())) {
                if ($position == 'right' && $upline->id == $sponsor->id) {
                    $data['position'] = $position;
                }
            } elseif ($position == 'right' && empty($directChild)) {
                $data['position'] = $position;
            }

            // Create the distributor with the provided parameters
            CoinMultiLevel::create($data);

            // Return success response
            return response()->json([
                'message' => trans('public.affiliate.add_distributor_message'),
            ]);
        }
    }

}    