<?php
namespace SimpleSoft\XePlugin\Loginlog\Controllers;

require __DIR__ . '/../../vendor/autoload.php';

use XeFrontend;
use XePresenter;
use XeDB;
use XeConfig;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use SimpleSoft\XePlugin\Loginlog\Plugin as Plugin;
use SimpleSoft\XePlugin\Loginlog\Models\LoginUserLog as LoginUserLogModel;

class LoginLogController extends Controller
{
	const CREATED_AT = 'logined_at';

    public function index(Request $request)
    {
        $title = '로그인 기록';

        // set browser title
        XeFrontend::title($title);

        // load css file
        XeFrontend::css(Plugin::asset('assets/style.css'))->load();

        $searchTargetWord = $request->get('keyfield');
		if($searchTargetWord)
		{
			 $keyword = '%' . $request->get('keyword') . '%';
		}

		$items = LoginUserLogModel::orderBy('log_id', 'desc');
		if($searchTargetWord && $keyword)
		{
			$items->where($searchTargetWord, 'LIKE', $keyword);
		}
        if ($startDate = $request->get('startDate')) {
            $items = $items->where('logined_at', '>=', $startDate . ' 00:00:00');
        }

        if ($endDate = $request->get('endDate')) {
            $items = $items->where('logined_at', '<=', $endDate . ' 23:59:59');
        }

		// pagination 사용
		$items = $items->paginate(15)->appends($request->except('page'));

		$searchedItems = LoginUserLogModel::orderBy('log_id', 'desc');
		if($searchTargetWord && $keyword)
		{
			$searchedItems->where($searchTargetWord, 'like', $keyword);
		}

        if ($startDate = $request->get('startDate')) {
            $searchedItems = $searchedItems->where('logined_at', '>=', $startDate . ' 00:00:00');
        }

        if ($endDate = $request->get('endDate')) {
            $searchedItems = $searchedItems->where('logined_at', '<=', $endDate . ' 23:59:59');
        }

		// 전체 기록 수
		$totalCount = LoginUserLogModel::count();
		// 검색된 기록 수
		$searchLogCount = $searchedItems->count();

        // output
        return XePresenter::make('loginlog::views.index', compact('items', 'searchedItems', 'totalCount', 'searchLogCount'));
    }
	
	protected function makeWhere()
	{
		
	}

	/**
     * Show confirm for delete a log.
     *
     * @param Request $request request
     * @return \Xpressengine\Presenter\Presentable
     */
    public function deletePage(Request $request)
    {
        $logIds = $request->get('logIds');

        $logIds = explode(',', $logIds);

        $logs = \SimpleSoft\XePlugin\Loginlog\Models\LoginUserLog::whereIn('log_id', $logIds)->get();

        return api_render('loginlog::views.delete', compact('logs'));
    }

    /**
     * Delete a log.
     *
     * @param Request $request request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function destroy(Request $request)
    {
        $logIds = $request->get('logId', []);

        XeDB::beginTransaction();
        try {
            XeDB::table('loginlog')->whereIn('log_id', $logIds)->delete();
        } catch (Exception $e) {
            XeDB::rollBack();
            throw $e;
        }
        XeDB::commit();

        return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::deleted')]);
    }

	public function setting()
	{	
		$config = XeConfig::get('loginlog');
		XeConfig::get('loginlog', ['admin_user_log' => 'Y' ]);

        return XePresenter::make('loginlog::views.setting', compact('config'));
	}

	public function saveAdminConfig(Request $request)
	{
		$data = $request->only('admin_user_log');

		$config = XeConfig::get('loginlog');
		XeConfig::set('loginlog', $data);
		
		 return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::saved')]);
	}
}
