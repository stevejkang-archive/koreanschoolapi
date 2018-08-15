<?php
/**
* koreanschoolmeal.php
* Created: Wednesday, Aug 15, 2018
* 
* Juneyoung KANG <juneyoungdev@gmail.com>
* Gyoha High School, 2nd Grade Student
*
* Creates school meal JSON data from the NEIS.
* Github : https://github.com/Juneyoung-Kang/koreanschoolapi/
*
* How to use?
* https://github.com/Juneyoung-Kang/koreanschoolapi/
* 
* For more information, visit github and read README.md
*
* Released under the GNU General Public License v3.
*/

error_reporting(E_ALL);                                             
header("Content-type: application/json; charset=UTF-8"); 

require "simple_html_dom.php"; 

$countryCode = $_GET['countryCode']; 
$schoolCode =  $_GET['schoolCode'];
$schoolName = $_GET['schoolName']; // optional
$schoolTypeCode = $_GET['schoolTypeCode']; 
// $schoolMealTypeCode = $_GET['schoolMealTypeCode'];
$resultType = $_GET['resultType']; // today, tomorrow, week, date
$schoolMealDate = $_GET['schoolMealDate']; // optional, 2018-08-07

if(strlen($countryCode)<13){                                  
    die('countryCode is too short!');
} elseif(strlen($countryCode)>13){
    die('countryCode is too long!');
} elseif(!isset($countryCode)){                              
    die('countryCode field is empty');
}

if(strlen($schoolCode)<10){                                  
    die('schoolCode is too short!');
} elseif(strlen($schoolCode)>10){
    die('schoolCode is too long!');
} elseif(!isset($schoolCode)){                              
    die('schoolCode field is empty');
}

if((int)$schoolTypeCode<1){
    die('schoolTypeCode is too small');
} elseif((int)$schoolTypeCode>4){
    die('schoolTypeCode is too big');
}

// if((int)$schoolMealTypeCode<1){
//     die('schoolMealTypeCode is too small');
// } elseif((int)$schoolMealTypeCode>3){
//     die('schoolMealTypeCode is too big');
// }

if($resultType == "today"){                   
    $date = date('Y.m.d');  
    $day = date('w');                       
} elseif($resultType == "tomorrow"){
    $date = date('Y.m.d', strtotime('24 hours', time()));       
    $day = date('w', strtotime('24 hours', time()));    
} elseif($resultType == "week"){
    $date = NULL;  
    $day = NULL;                                              
} elseif($resultType == "date"){
    if(isset($schoolMealDate)){
        $schoolMealDate4URL = str_replace('-', '.', $schoolMealDate);
        $date = $schoolMealDate4URL;
        $day = date('w', strtotime($schoolMealDate));
    } else{
        die('schoolMealDate field is empty');
    }
} elseif(!isset($resultType)){
    die('resultType field is empty');             
} else {
    die('resultType field is wrong');          
}

$dom = new DOMDocument;

function mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, $schoolMealTypeCode, $date, $day){
    $url = "http://".$countryCode."/sts_sci_md01_001.do?schulCode=".$schoolCode."&insttNm=".urlencode($schoolName)."&schulCrseScCode=".$schoolTypeCode."&schMmealScCode=".$schoolMealTypeCode."&schYmd=".$date;
    $html = $dom->loadHTMLFile($url);
    $dom->preserveWhiteSpace = false;

    $table=$dom->getElementsByTagName('table');
    $tbody=$table->item(0)->getElementsByTagName('tbody');
    $rows=$tbody->item(0)->getElementsByTagName('tr');
    $cols=$rows->item(1)->getElementsByTagName('td');

    if($cols->item($day)->nodeValue == null){
        return NULL;
    } elseif($cols->item($day)->nodeValue == " "){
        return '급식이 없습니다.';
    } else{
        $final = $cols->item($day)->nodeValue;
        $final = preg_replace("/[0-9]/", "", $final);
        $final = str_replace(".", "", $final);
        return $final;
    }
}             

function dayKorean($day){                                    
    switch($day){
        case '0': $day_kr = "일요일"; break;
        case '1': $day_kr = "월요일"; break;
        case '2': $day_kr = "화요일"; break;
        case '3': $day_kr = "수요일"; break;
        case '4': $day_kr = "목요일"; break;
        case '5': $day_kr = "금요일"; break;
        case '6': $day_kr = "토요일"; break;
    }
    return $day_kr;
}

function schoolTypeCodeCheck($schoolTypeCode){                                    
    switch($schoolTypeCode){
        case '1': $schoolTypeCodeResult = "유치원"; break;
        case '2': $schoolTypeCodeResult = "초등학교"; break;
        case '3': $schoolTypeCodeResult = "중학교"; break;
        case '4': $schoolTypeCodeResult = "고등학교"; break;
    }
    return $schoolTypeCodeResult;
}

// function schoolMealTypeCodeCheck($schoolMealTypeCode){                                    
//     switch($schoolTypeCode){
//         case '1': $schoolMealTypeCodeResult = "조식"; break;
//         case '2': $schoolMealTypeCodeResult = "중식"; break;
//         case '3': $schoolMealTypeCodeResult = "석식"; break;
//     }
//     return $schoolMealTypeCodeResult;
// }

switch($resultType){
    case "today":
        $array = array(
            'apiName' => 'koreanschoolmeal',
            'data' => array(
                'schoolName' => $schoolName,
                'countryCode' => $countryCode,
                'schoolCode' => $schoolCode,
                'schoolType' => schoolTypeCodeCheck($schoolTypeCode),
                // 'mealType' => schoolMealTypeCodeCheck($schoolMealTypeCode),
                'resultType' => $resultType,
                'timeStamp' => date('Y.m.d H:i:s'),
                'result' => array(
                    'date' => date('Y.m.d'),
                    'day' => dayKorean(date('w')),
                    'breakfast' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 1, $date, $day),
                    'lunch' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 2, $date, $day),
                    'dinner' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 3, $date, $day)
                )
            )
        );
        break;
    case "date":
        $array = array(
            'apiName' => 'koreanschoolmeal',
            'data' => array(
                'schoolName' => $schoolName,
                'countryCode' => $countryCode,
                'schoolCode' => $schoolCode,
                'schoolType' => schoolTypeCodeCheck($schoolTypeCode),
                // 'mealType' => schoolMealTypeCodeCheck($schoolMealTypeCode),
                'resultType' => $resultType,
                'timeStamp' => date('Y.m.d H:i:s'),
                'result' => array(
                    'date' => $date,
                    'day' => dayKorean($day),
                    'breakfast' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 1, $date, $day),
                    'lunch' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 2, $date, $day),
                    'dinner' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 3, $date, $day)
                )
            )
        );
        break;
    case "week":
        $day = date('w');
        if($day==0){$time_init='+24 hours';$time_2='+48 hours';$time_3='+72 hours';$time_4='+96 hours';$time_5='+120 hours';}
        elseif($day==1){$time_init='+0 hours';$time_2='+24 hours';$time_3='+48 hours';$time_4='+72 hours';$time_5='+96 hours';}
        elseif($day==2){$time_init='-24 hours';$time_2='+0 hours';$time_3='+24 hours';$time_4='+48 hours';$time_5='+72 hours';}
        elseif($day==3){$time_init='-48 hours';$time_2='-24 hours';$time_3='+0 hours';$time_4='+24 hours';$time_5='+48 hours';}
        elseif($day==4){$time_init='-72 hours';$time_2='-48 hours';$time_3='-24 hours';$time_4='+0 hours';$time_5='+24 hours';}
        elseif($day==5){$time_init='-96 hours';$time_2='-72 hours';$time_3='-48 hours';$time_4='-24 hours';$time_5='+0 hours';}
        elseif($day==6){$time_init='-120 hours';$time_2='-96 hours';$time_3='-72 hours';$time_4='-48 hours';$time_5='-24 hours';}
        $array = array(
            'apiName' => 'koreanschoolmeal',
            'data' => array(
                'schoolName' => $schoolName,
                'countryCode' => $countryCode,
                'schoolCode' => $schoolCode,
                'schoolType' => schoolTypeCodeCheck($schoolTypeCode),
                // 'mealType' => schoolMealTypeCodeCheck($schoolMealTypeCode),
                'resultType' => $resultType,
                'timeStamp' => date('Y.m.d H:i:s'),
                'result' => array(
                    0 => array(
                        'date' => date('Y.m.d', strtotime($time_init, time())),
                        'day' => dayKorean(date('w', strtotime($time_init, time()))),
                        'breakfast' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 1, date('Y.m.d', strtotime($time_init, time())), date('w', strtotime($time_init, time()))),
                        'lunch' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 2, date('Y.m.d', strtotime($time_init, time())), date('w', strtotime($time_init, time()))),
                        'dinner' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 3, date('Y.m.d', strtotime($time_init, time())), date('w', strtotime($time_init, time())))
                    ),
                    1 => array(
                        'date' => date('Y.m.d', strtotime($time_2, time())),
                        'day' => dayKorean(date('w', strtotime($time_2, time()))),
                        'breakfast' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 1, date('Y.m.d', strtotime($time_2, time())), date('w', strtotime($time_2, time()))),
                        'lunch' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 2, date('Y.m.d', strtotime($time_2, time())), date('w', strtotime($time_2, time()))),
                        'dinner' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 3, date('Y.m.d', strtotime($time_2, time())), date('w', strtotime($time_2, time())))
                    ),
                    2 => array(
                        'date' => date('Y.m.d', strtotime($time_3, time())),
                        'day' => dayKorean(date('w', strtotime($time_3, time()))),
                        'breakfast' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 1, date('Y.m.d', strtotime($time_3, time())), date('w', strtotime($time_3, time()))),
                        'lunch' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 2, date('Y.m.d', strtotime($time_3, time())), date('w', strtotime($time_3, time()))),
                        'dinner' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 3, date('Y.m.d', strtotime($time_3, time())), date('w', strtotime($time_3, time())))
                    ),
                    3 => array(
                        'date' => date('Y.m.d', strtotime($time_4, time())),
                        'day' => dayKorean(date('w', strtotime($time_4, time()))),
                        'breakfast' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 1, date('Y.m.d', strtotime($time_4, time())), date('w', strtotime($time_4, time()))),
                        'lunch' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 2, date('Y.m.d', strtotime($time_4, time())), date('w', strtotime($time_4, time()))),
                        'dinner' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 3, date('Y.m.d', strtotime($time_4, time())), date('w', strtotime($time_4, time())))
                    ),
                    4 => array(
                        'date' => date('Y.m.d', strtotime($time_5, time())),
                        'day' => dayKorean(date('w', strtotime($time_5, time()))),
                        'breakfast' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 1, date('Y.m.d', strtotime($time_5, time())), date('w', strtotime($time_5, time()))),
                        'lunch' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 2, date('Y.m.d', strtotime($time_5, time())), date('w', strtotime($time_5, time()))),
                        'dinner' => mealResult($dom, $countryCode, $schoolCode, $schoolName, $schoolTypeCode, 3, date('Y.m.d', strtotime($time_5, time())), date('w', strtotime($time_5, time())))
                    )
                )
            )
        );
        break;
}

$json = json_encode($array, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
echo $json;
?>