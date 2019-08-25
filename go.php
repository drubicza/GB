<?php
error_reporting(0);
require "curl.php";
echo "\n\nThank To : Muhammad Ikhsan Aprilyadi\nGoBills Casback 70%\n\nNo HP : ";
$phone        = trim(fgets(STDIN));
$bearer       = "";
$useruuid     = "";
$uniqueid     = rand(1000,9999).rand(100,999)."e".rand(10,99)."ff".rand(100,999)."b";
$header_login = array("X-AppVersion: 3.22.1",
                      "X-UniqueId: ".$uniqueid,
                      "X-Platform: Android",
                      "X-AppId: com.gojek.app",
                      "Accept: application/json",
                      "X-Session-ID: 74f5927f-85fc-487d-891e-5a3ca3f6736e",
                      "D1: 58:2B:4B:CE:96:77:65:D1:20:81:C0:B2:75:ED:5D:A2:C1:14:D4:60:D8:2E:E1:D1:88:5C:8B:95:C6:40:AA:31",
                      "X-PhoneModel: xiaomi,Redmi 6",
                      "X-PushTokenType: FCM",
                      "X-DeviceOS: Android,8.1.0",
                      "User-uuid: ",
                      "X-DeviceToken: ",
                      "Authorization: Bearer",
                      "Accept-Language: id-ID",
                      "X-Location: -6.11968,106.1973892",
                      'X-M1: 1:__31f4c8c2f95246f0833f1283c1d02cdb,2:c71b9b0b7d24,3:1565998740752-5544313850786010885,4:24519,5:mt6765|2001|8,6:0C:98:38:CB:1A:87,7:"XLGO-83C6",8:720x1344,9:passive\,network,10:0,11:sHLp9psghlEJimfsIzXKhptQnGhigYRUifllHhizjNg=',
                      "Content-Type: application/json; charset=UTF-8",
                      "Connection: Keep-Alive",
                      "User-Agent: okhttp/3.12.1");

$deviceId         = rand(100,999)."b6d3c-".rand(1000,9999)."-".rand(1000,9999)."-9fb7-b1".rand(10000,99999)."e4c15";
$login_with_phone = request("https://api.gojekapi.com/v4/customers/login_with_phone",
                            '{"phone":"+'.$phone.'"}',
                            null,
                            $header_login);

if (preg_match("/Nomor HP ini tidak valid/",$login_with_phone[1])) {
    $regis = false;
    echo " +62".$phone." Nomor Belum Terdaftar\n";
} else if (preg_match("/Kode verifikasi sudah dikirim/",$login_with_phone[1])) {
    $regis     = false;
    $otp_token = json_decode($login_with_phone[1])->data->login_token;

    echo "".json_decode($login_with_phone[1])->data->message."\nKode OTP : ";
    $otp    = trim(fgets(STDIN));
    $verify = request("https://api.gojekapi.com/v4/customers/login/verify",
                      '{"client_name":"gojek:cons:android","client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e","data":{"otp":"'.$otp.'","otp_token":"'.$otp_token.'"},"grant_type":"otp","scopes":"gojek:customer:transaction gojek:customer:readonly"}',
                      null,
                      $header_login);

    if (preg_match("/access_token/",$verify[1])) {
        $bearer       = json_decode($verify[1])->data->access_token;
        $uuid         = json_decode($verify[1])->data->customer->id;
        $header_login = array("X-AppVersion: 3.22.1",
                              "X-UniqueId: ".$uniqueid,
                              "X-Platform: Android",
                              "X-AppId: com.gojek.app",
                              "Accept: application/json",
                              "X-Session-ID: 74f5927f-85fc-487d-891e-5a3ca3f6736e",
                              "D1: 58:2B:4B:CE:96:77:65:D1:20:81:C0:B2:75:ED:5D:A2:C1:14:D4:60:D8:2E:E1:D1:88:5C:8B:95:C6:40:AA:31",
                              "X-PhoneModel: xiaomi,Redmi 6",
                              "X-PushTokenType: FCM",
                              "X-DeviceOS: Android,8.1.0",
                              "User-uuid: ".$uuid,
                              "X-DeviceToken: ",
                              "Authorization: Bearer ".$bearer,
                              "Accept-Language: en-ID",
                              "X-Location: -6.11968,106.1973892",
                              'X-M1: 1:__31f4c8c2f95246f0833f1283c1d02cdb,2:c71b9b0b7d24,3:1565998740752-5544313850786010885,4:24519,5:mt6765|2001|8,6:0C:98:38:CB:1A:87,7:"XLGO-83C6",8:720x1344,9:passive\,network,10:0,11:sHLp9psghlEJimfsIzXKhptQnGhigYRUifllHhizjNg=',
                              "Content-Type: application/json; charset=UTF-8",
                              "Connection: Keep-Alive",
                              "User-Agent: okhttp/3.12.1");
        $order = request("https://api.gojekapi.com/gopoints/v1/orders",
                         '{"gopay_pin":"","payment_type":"points","voucher_batch_id":"4fa052bd-c8d9-4aa4-bf43-8801c5447920","voucher_count":1}',
                         null,
                         $header_login);
        $ambil = json_decode($order[1])->data->voucher_codes;

        echo "\n ".$order[1]."\n\n";
    } else {
        $regis = false;
        echo " Kode OTP Salah\n";
    }
} else {
    $regis = false;
    echo "Ada Yang Salah\n";
}
?>
