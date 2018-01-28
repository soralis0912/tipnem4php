<?php
/**
 * @copyright @soralis_nem All Rights Reserved
 * @license https://opensource.org/licenses/mit-license.html MIT License
 * @author soralis <soralis.nem@gmail.com>
 * @link https://namuyan.github.io/nem-tip-bot/doc/websocket_api.html
 */
namespace soralis_nem\tipnem4php;
use WebSocket\Client;
/**
 * WebSocketのクライアントです。
 * @access public
 */
class WSClient
{
	const SERVER_URL = "ws://tipnem.tk:8088";
	private $connection;

/**
 * Webソケットを接続します。
 *
 * @return stdClassObject
 */
	function connect()
	{
		$this->connection = new Client(self::SERVER_URL);
		$this->connection->send("");
		$resdata =json_decode($this->connection->receive());
		return $resdata;
	}
/**
 * tipbot の状態を返します。
 * uptimeはサーバーが起動を開始してからの秒数を返します
 * ws_userは現在コネクトされているユーザーの数です。
 * threadはサーバー内の起動しているスレッド群を示します。
 *
 * @return stdClassObject
 */
	function bot_info()
	{
		return $this->_send("bot/info");
	}
/**
 * ユーザーの公開情報を返します。
 * user_code はログインユーザーに与えられるユニークなHEXコードです。
 *
 * @return stdClassObject
 */
	function user_info()
	{
		return $this->_send("user/info");
	}
/**
 * ユーザーアカウントにログインします。
 * tipbot よりDM経由でPINコードが送られてきますので user_check より入力して下さい。
 * 何者かが不正にアクセスしてPINコードが発行された可能性がある場合は、user_checkへ絶対に入力しないで下さい。
 *
 * @param String $screen_name
 * @return stdClassObject
 */
	function user_offer($screen_name)
	{
		return $this->_send("user/offer",["screen_name"=>$screen_name]);
	}
/**
 * PINコードの入力に失敗した場合、DM経由でユーザーにレポートされます。
 * PINコードはワンタイムトークンです。入力に失敗した場合、PINコードの再発行が必要になります。
 *
 * @param String $screen_name
 * @param String $pincode
 * @return stdClassObject
 */
	function user_check($screen_name,$pincode)
	{
		return $this->_send("user/check",["screen_name"=>$screen_name,"pincode"=>$pincode]);
	}
/**
 * level を2以上に昇格します。既に level=1 である事が必要です。
 * 出金・投げ銭する場合に必要になります。
 * DM経由でPINコードが発行されます。
 * またlevel1以上のユーザーはレベルを下げることもできます。PIN認証は必要ありません。
 *
 * @param String $require_level
 * @return stdClassObject
 */
	function user_upgrade($require_level)
	{
		return $this->_send("user/upgrade",["require_level"=>$require_level]);
	}
/**
 * WebSocketっでコマンドを送信します。
 *
 * @param String $command
 * @param String $data
 * @return stdClassObject
 */
	function _send($command="",$data=[])
	{
		$uuid = mt_rand(1, 2147483647);
		$param = ["command"=>$command,"data"=>$data,"uuid"=>$uuid];
		$json = json_encode($param);
		$this->connection->send($json);
		$resdata =json_decode($this->connection->receive());
		return $resdata;
	}
}
