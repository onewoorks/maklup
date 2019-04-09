<?php

class Onewaysms {

    private static $apiusername = "APIDMR231QSQJ";
    private static $apiuserpass = "APIDMR231QSQJJ8AUB";
    private static $mturl = 'http://gateway.onewaysms.com.my:10001/api.aspx';
    private static $sender = 'pulkam2019';
    private static $fixmsg = 'PULKAM2019 : ';

    public static function SendSMS($mobile_no, $message) {
        $query = self::$mturl . "?apiusername=" . self::$apiusername . ""
                . "&apipassword=" . self::$apiuserpass . ""
                . "&mobileno=$mobile_no"
                . "&senderid=" . self::$sender . ""
                . "&message=" . urlencode(self::$fixmsg.$message) . "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY,1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

}
