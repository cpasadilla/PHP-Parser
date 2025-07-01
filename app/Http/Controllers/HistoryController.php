<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderUpdateLog;
use App\Models\OrderDeleteLog;
use App\Models\Order;
use Illuminate\Pagination\Paginator;
use App\Models\User; // Add this import

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $nameFilter = $request->input('name');
        $updatedByFilter = $request->input('updated_by');
        $deletedByFilter = $request->input('deleted_by');
        $restoreStatusFilter = $request->input('restore_status');
        $fieldNameFilter = $request->input('field_name');
        $actionTypeFilter = $request->input('action_type');

        // Fetch user activity logs from the sessions table with pagination and filtering
        $userActivities = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select(DB::raw("CONCAT(users.fName, ' ', users.lName) as name"), 'sessions.ip_address', 'sessions.last_activity')
            ->when($nameFilter, function ($query, $nameFilter) {
                return $query->where(DB::raw("CONCAT(users.fName, ' ', users.lName)"), 'like', "%$nameFilter%");
            })
            ->orderBy('sessions.last_activity', 'desc')
            ->paginate(10, ['*'], 'userActivitiesPage');

        // Fetch order update logs with pagination and filtering
        $orderUpdateLogs = OrderUpdateLog::select('order_update_logs.*')
            ->join('orders', 'order_update_logs.order_id', '=', 'orders.id')
            ->select(
                'order_update_logs.*',
                'orders.shipNum as ship_name',
                'orders.voyageNum as voyage_number',
                'orders.orderId as bl_number'
            )
            ->when($updatedByFilter, function ($query, $updatedByFilter) {
                return $query->where('order_update_logs.updated_by', 'like', "%$updatedByFilter%");
            })
            ->when($fieldNameFilter, function ($query, $fieldNameFilter) {
                return $query->where('order_update_logs.field_name', $fieldNameFilter);
            })
            ->when($actionTypeFilter, function ($query, $actionTypeFilter) {
                return $query->where('order_update_logs.action_type', $actionTypeFilter);
            })
            ->orderBy('order_update_logs.updated_at', 'desc')
            ->paginate(10, ['*'], 'orderUpdateLogsPage');

        // Fetch order delete logs with pagination and filtering
        $orderDeleteLogs = OrderDeleteLog::query()
            ->when($deletedByFilter, function ($query, $deletedByFilter) {
                return $query->where('deleted_by', 'like', "%$deletedByFilter%");
            })
            ->when($restoreStatusFilter, function ($query, $restoreStatusFilter) {
                if ($restoreStatusFilter === 'deleted') {
                    return $query->whereNull('restored_at');
                } elseif ($restoreStatusFilter === 'restored') {
                    return $query->whereNotNull('restored_at');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'orderDeleteLogsPage');

        // Fetch all users for the filter dropdown
        $allUsers = User::select(DB::raw("CONCAT(fName, ' ', lName) as name"))->get();

        return view('history.index', [
            'userActivities' => $userActivities,
            'orderUpdateLogs' => $orderUpdateLogs,
            'orderDeleteLogs' => $orderDeleteLogs,
            'allUsers' => $allUsers, // Pass all users to the view
        ]);
    }
}
