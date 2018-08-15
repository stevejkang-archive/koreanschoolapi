<?php
/**
* koreanschooltt.php
* Created: Tuesday, Jun 27, 2018
* 
* Juneyoung KANG <juneyoungdev@gmail.com>
* Gyoha High School, 2nd Grade Student
*
* Creates timetable JSON data from the comcigan.
* Github : https://github.com/Juneyoung-Kang/koreanschoolapi/
*
* How to use?
* https://github.com/Juneyoung-Kang/koreanschoolapi/
* 
* For more information, visit github and read README.md
*
* Released under the GNU General Public License v3.
*/

error_reporting(0);                                             
header("Content-type: application/json; charset=UTF-8"); 

$schoolName = $_GET['schoolName'];                
$gradeNumber = $_GET['gradeNumber'];                   
$classNumber = $_GET['classNumber'];                   
$resultType = $_GET['resultType'];                     

if(strlen($schoolName)<3){                                  
    die('schoolName is too short!');
} elseif(strlen($schoolName)>100){
    die('schoolName is too long!');
} elseif(!isset($schoolName)){                              
    die('schoolName field is empty');
}

if(!isset($gradeNumber)){
    die('gradeNumber field is empty');
} elseif(!isset($classNumber)){
    die('classNumber field is empty');
}

if(strlen($gradeNumber)>2){
    die('gradeNumber is too long');
} elseif(strlen($classNumber)>3){
    die('classNumber is too long');
}

if($resultType == "today"){                   
    $day = date('w');                             
} elseif($resultType == "tomorrow"){
    $day = date('w', strtotime('24 hours', time()));          
} elseif($resultType == "week"){
    $day = NULL;                                                
} elseif(!isset($resultType)){
    die('resultType field is empty');             
} else {
    die('resultType field is wrong');          
}

$schoolName_euckr = iconv("UTF-8", "EUC-KR", $schoolName);    

$gradeNumber = (int)$gradeNumber;                              
$classNumber = (int)$classNumber;

$url_code = "http://comci.kr:4081/98372?92744l".urlencode($schoolName_euckr);      
$json_code = file_get_contents($url_code);                   
$json_code = stripslashes(html_entity_decode($json_code));
$array_code = json_decode(trim($json_code), true);        
$result_code = $array_code['학교검색'][0][3];                 
if($result_code==NULL){
    die('school is not compatible with this api');          
}

$tt_code = "34739_".$result_code."_0_1";               
$ttcode_base64 = base64_encode($tt_code);                    

$url_tt = "http://comci.kr:4081/98372?".$ttcode_base64;  
$json_tt = file_get_contents($url_tt);                     
$json_tt = stripslashes(html_entity_decode($json_tt));          
$array_tt = json_decode(trim($json_tt), true);                
$result_tt = $array_tt['자료81'][$gradeNumber][$classNumber]; 

$result_teacher = $array_tt['자료46'];                          
$result_subject = $array_tt['긴자료92'];                      

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

function result($class, $result_tt, $result_teacher, $result_subject, $day){     
    $class_info = $result_tt[$day][$class];               
    $subject = substr($class_info, -2, 2);    
    $subject = (int)$subject;                                 
    $subject_final = $result_subject[$subject];             
    $teacher = substr($class_info, -4, -2);                
    $teacher = (int)$teacher;                                
    $teacher_final = $result_teacher[$teacher];          
    $result = $subject_final.'('.$teacher_final.')';         
    if($result=='(  *)'){                                      
        return NULL;                                        
    } else if($result=='()'){
        usleep(100000);
        header("Refresh:0");
    }
    return $result;                                         
}

switch($resultType){
    case "today":
        $array = array(
            'apiName' => 'koreanschooltt',
            'data' => array(
                'schoolName' => $schoolName,
                'gradeNumber' => $gradeNumber,
                'classNumber' => $classNumber,
                'resultType' => $resultType,
                'timeStamp' => date('Y.m.d H:i:s'),
                'result' => array(
                    'date' => date('Y.m.d'),
                    'day' => dayKorean(date('w')),
                    'class01' => result(1, $result_tt, $result_teacher, $result_subject, $day),
                    'class02' => result(2, $result_tt, $result_teacher, $result_subject, $day),
                    'class03' => result(3, $result_tt, $result_teacher, $result_subject, $day),
                    'class04' => result(4, $result_tt, $result_teacher, $result_subject, $day),
                    'class05' => result(5, $result_tt, $result_teacher, $result_subject, $day),
                    'class06' => result(6, $result_tt, $result_teacher, $result_subject, $day),
                    'class07' => result(7, $result_tt, $result_teacher, $result_subject, $day),
                    'class08' => result(8, $result_tt, $result_teacher, $result_subject, $day),
                    'class09' => result(9, $result_tt, $result_teacher, $result_subject, $day),
                    'class10' => result(10, $result_tt, $result_teacher, $result_subject, $day)
                )
            )
        );
        break;
    case "tomorrow":
        $array = array(
            'apiName' => 'koreanschooltt',
            'data' => array(
                'schoolName' => $schoolName,
                'gradeNumber' => $gradeNumber,
                'classNumber' => $classNumber,
                'resultType' => $resultType,
                'timeStamp' => date('Y.m.d H:i:s'),
                'result' => array(
                    'date' => date('Y.m.d', strtotime('+24 hours', time())),
                    'day' => dayKorean(date('w', strtotime('+24 hours', time()))),
                    'class01' => result(1, $result_tt, $result_teacher, $result_subject, $day),
                    'class02' => result(2, $result_tt, $result_teacher, $result_subject, $day),
                    'class03' => result(3, $result_tt, $result_teacher, $result_subject, $day),
                    'class04' => result(4, $result_tt, $result_teacher, $result_subject, $day),
                    'class05' => result(5, $result_tt, $result_teacher, $result_subject, $day),
                    'class06' => result(6, $result_tt, $result_teacher, $result_subject, $day),
                    'class07' => result(7, $result_tt, $result_teacher, $result_subject, $day),
                    'class08' => result(8, $result_tt, $result_teacher, $result_subject, $day),
                    'class09' => result(9, $result_tt, $result_teacher, $result_subject, $day),
                    'class10' => result(10, $result_tt, $result_teacher, $result_subject, $day)
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
            'apiName' => 'koreanschooltt',
            'data' => array(
                'schoolName' => $schoolName,
                'gradeNumber' => $gradeNumber,
                'classNumber' => $classNumber,
                'resultType' => $resultType,
                'timeStamp' => date('Y.m.d H:i:s'),
                'result' => array(
                    0 => array(
                        'date' => date('Y.m.d', strtotime($time_init, time())),
                        'day' => dayKorean(date('w', strtotime($time_init, time()))),
                        'class01' => result(1, $result_tt, $result_teacher, $result_subject, 1),
                        'class02' => result(2, $result_tt, $result_teacher, $result_subject, 1),
                        'class03' => result(3, $result_tt, $result_teacher, $result_subject, 1),
                        'class04' => result(4, $result_tt, $result_teacher, $result_subject, 1),
                        'class05' => result(5, $result_tt, $result_teacher, $result_subject, 1),
                        'class06' => result(6, $result_tt, $result_teacher, $result_subject, 1),
                        'class07' => result(7, $result_tt, $result_teacher, $result_subject, 1),
                        'class08' => result(8, $result_tt, $result_teacher, $result_subject, 1),
                        'class09' => result(9, $result_tt, $result_teacher, $result_subject, 1),
                        'class10' => result(10, $result_tt, $result_teacher, $result_subject, 1)
                    ),
                    1 => array(
                        'date' => date('Y.m.d', strtotime($time_2, time())),
                        'day' => dayKorean(date('w', strtotime($time_2, time()))),
                        'class01' => result(1, $result_tt, $result_teacher, $result_subject, 2),
                        'class02' => result(2, $result_tt, $result_teacher, $result_subject, 2),
                        'class03' => result(3, $result_tt, $result_teacher, $result_subject, 2),
                        'class04' => result(4, $result_tt, $result_teacher, $result_subject, 2),
                        'class05' => result(5, $result_tt, $result_teacher, $result_subject, 2),
                        'class06' => result(6, $result_tt, $result_teacher, $result_subject, 2),
                        'class07' => result(7, $result_tt, $result_teacher, $result_subject, 2),
                        'class08' => result(8, $result_tt, $result_teacher, $result_subject, 2),
                        'class09' => result(9, $result_tt, $result_teacher, $result_subject, 2),
                        'class10' => result(10, $result_tt, $result_teacher, $result_subject, 2)
                    ),
                    2 => array(
                        'date' => date('Y.m.d', strtotime($time_3, time())),
                        'day' => dayKorean(date('w', strtotime($time_3, time()))),
                        'class01' => result(1, $result_tt, $result_teacher, $result_subject, 3),
                        'class02' => result(2, $result_tt, $result_teacher, $result_subject, 3),
                        'class03' => result(3, $result_tt, $result_teacher, $result_subject, 3),
                        'class04' => result(4, $result_tt, $result_teacher, $result_subject, 3),
                        'class05' => result(5, $result_tt, $result_teacher, $result_subject, 3),
                        'class06' => result(6, $result_tt, $result_teacher, $result_subject, 3),
                        'class07' => result(7, $result_tt, $result_teacher, $result_subject, 3),
                        'class08' => result(8, $result_tt, $result_teacher, $result_subject, 3),
                        'class09' => result(9, $result_tt, $result_teacher, $result_subject, 3),
                        'class10' => result(10, $result_tt, $result_teacher, $result_subject, 3)
                    ),
                    3 => array(
                        'date' => date('Y.m.d', strtotime($time_4, time())),
                        'day' => dayKorean(date('w', strtotime($time_4, time()))),
                        'class01' => result(1, $result_tt, $result_teacher, $result_subject, 4),
                        'class02' => result(2, $result_tt, $result_teacher, $result_subject, 4),
                        'class03' => result(3, $result_tt, $result_teacher, $result_subject, 4),
                        'class04' => result(4, $result_tt, $result_teacher, $result_subject, 4),
                        'class05' => result(5, $result_tt, $result_teacher, $result_subject, 4),
                        'class06' => result(6, $result_tt, $result_teacher, $result_subject, 4),
                        'class07' => result(7, $result_tt, $result_teacher, $result_subject, 4),
                        'class08' => result(8, $result_tt, $result_teacher, $result_subject, 4),
                        'class09' => result(9, $result_tt, $result_teacher, $result_subject, 4),
                        'class10' => result(10, $result_tt, $result_teacher, $result_subject, 4)
                    ),
                    4 => array(
                        'date' => date('Y.m.d', strtotime($time_5, time())),
                        'day' => dayKorean(date('w', strtotime($time_5, time()))),
                        'class01' => result(1, $result_tt, $result_teacher, $result_subject, 5),
                        'class02' => result(2, $result_tt, $result_teacher, $result_subject, 5),
                        'class03' => result(3, $result_tt, $result_teacher, $result_subject, 5),
                        'class04' => result(4, $result_tt, $result_teacher, $result_subject, 5),
                        'class05' => result(5, $result_tt, $result_teacher, $result_subject, 5),
                        'class06' => result(6, $result_tt, $result_teacher, $result_subject, 5),
                        'class07' => result(7, $result_tt, $result_teacher, $result_subject, 5),
                        'class08' => result(8, $result_tt, $result_teacher, $result_subject, 5),
                        'class09' => result(9, $result_tt, $result_teacher, $result_subject, 5),
                        'class10' => result(10, $result_tt, $result_teacher, $result_subject, 5)
                    )
                )
            )
        );
        break;
}

$json = json_encode($array, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
echo $json;
?>