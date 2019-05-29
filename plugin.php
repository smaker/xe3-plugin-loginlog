<?php
namespace SimpleSoft\XePlugin\Loginlog;

use Xpressengine\Plugin\AbstractPlugin;
use XeConfig;
use Route;
use Presenter;
use Schema;
use XeRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Plugin extends AbstractPlugin
{
    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
		if (XeConfig::get('loginlog') == null)
		{
            XeConfig::add('loginlog', []);
		}
		
		$this->route();
		$this->intercept();
    }

	/**
	 * route 등록
	 */
    protected function route()
    {
		require __DIR__ . '/routes.php';

		XeRegister::push(
			'settings/menu',
			'user.loginlog',
			[
				'title' => '로그인 기록',
				'display' => true,
				'description' => '',
				'ordering' => 360
			]
		);
		
		XeRegister::push(
			'settings/menu',
			'setting.loginlog',
			[
				'title' => '로그인 기록 설정',
				'display' => true,
				'description' => '',
				'ordering' => 360
			]
		);
	}

	/**
	 * intercept 등록
	 */ 
    private function intercept()
    {
		intercept(
            \Xpressengine\User\Guard::class . '@attempt',
            'loginlog_auth_:attempt',
            function ($func, array $credentials = [], $remember = false) {
				$result = $func($credentials, $remember);
				
				$user = \Auth::user();

				$res = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

                // 로그인 후
                if ($result == false)
				{
                    // 로그인 실패
					\XeDB::table('loginlog')->insert(
						['user_id' => $user->id,
						 'display_name' => $user->display_name,
						 'email' => $user->email,
						 'ipaddress' => \Request::ip(),
						 'is_succeed' => 'N',
						 'platform' => $res->os->toString(),
						 'browser' => $res->browser->toString()
						]
					);
                }

                return $result;
            }
        );

		intercept(
			'Auth@login',
			'loginlog_auth_::login',
			function ($func, $user, $remember = false) {
				$res = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

				$func($user, $remember);

				// 최고 관리자인 경우
				if($user->rating == 'super')
				{
					$config = XeConfig::get('loginlog');
					if($config->get('admin_user_log') == 'Y')
					{
						// 로그인 성공
						\XeDB::table('loginlog')->insert(
							['user_id' => $user->id,
							 'display_name' => $user->display_name,
							 'email' => $user->email,
							 'ipaddress' => \Request::ip(),
							 'is_succeed' => 'Y',
							 'platform' => $res->os->toString(),
							 'browser' => $res->browser->toString()
							]
						);
					}
				}
				else
				{
					// 로그인 성공
					\XeDB::table('loginlog')->insert(
						['user_id' => $user->id,
						 'display_name' => $user->display_name,
						 'email' => $user->email,
						 'ipaddress' => \Request::ip(),
						 'is_succeed' => 'Y',
						 'platform' => $res->os->toString(),
						 'browser' => $res->browser->toString()
						]
					);
				}
			}
		);
    }

	/**
     * 플러그인이 활성화될 때 실행할 코드를 여기에 작성한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function activate($installedVersion = null)
    {
        // implement code
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
		$this->createSchema();
    }

	/**
	 * 스키마 생성
	 *
	 * @return void
	 */
	protected function createSchema()
	{
		Schema::create('loginlog', function ($table) {
			$table->engine = 'InnoDB';

			$table->bigIncrements('log_id');
			$table->string('user_id', 255);
			$table->string('display_name', 255);
			$table->string('email', 255);
			$table->ipAddress('ipaddress');
			$table->enum('is_succeed', ['Y', 'N']);
			$table->string('platform', 32);
			$table->string('browser', 32);
			$table->timestamp('logined_at');
		});
	}

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled()
    {
       // 테이블이 존재하는지 검사, 없으면 false를 반환
		return Schema::hasTable('loginlog');
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @return void
     */
    public function update()
    {
        // implement code
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        // implement code

        return parent::checkUpdated();
    }
}
