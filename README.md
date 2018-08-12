# KoreanSchoolAPI
전국초중고등학교 시간표/학사일정/급식 종합 API [Korean School Timetable/Schedule/Meal API]

## Requirements
- git
- docker

## Installation

1. git clone
```
$ git clone https://github.com/Juneyoung-Kang/koreanschoolapi.git  
$ cd koreanschoolapi
```
2. pull and run docker image
```
$ docker pull juneyoungdev/docker-app:latest
$ docker run -p 4567:80 juneyoungdev/docker-app:latest
```

## How to use
send GET requset to endpoint
```
curl -XGET "http://localhost:4567/koreanschooltt.php?schoolName=교하고&gradeNumber=2&classNumber=6&resultType=week"
```

## API Specification
### KoreanSchoolTT
- Method: GET
- URL: http://localhost:4567/koreanschooltt.php
- Content-Type: application/json; charset=utf-8
- Parameters  

| Request | Field Name | Details | Type | Required | Example Value |
|---------|-------------|----------|--------|----------|---------------|
| GET | schoolName | 학교이름 | String | Y | `교하고` |
| GET | gradeNumber | 학년 | String | Y | `2` |
| GET | classNumber | 반 | String | Y | `6` |
| GET | resultType | 결과타입 | String | Y | `today`/`tomorrow`/`week` |

- Example
```
curl -XGET "http://localhost:4567/koreanschooltt.php?schoolName=교하고&gradeNumber=2&classNumber=6&resultType=week"
```
- Response:
```
{
    "apiName": "koreanschooltt",
    "data": {
        "schoolName": "교하고",
        "gradeNumber": 2,
        "classNumber": 6,
        "resultType": "week",
        "timeStamp": "2018.08.12 00:23:32",
        "result": [
            {
                "date": "2018.08.13",
                "day": "월요일",
                "class01": "스포츠문화(박용*)",
                "class02": "독서와문법(변미*)",
                "class03": "미술창작(김혜*)",
                "class04": "화학Ⅰ(김다*)",
                "class05": "미적분Ⅱ(김병*)",
                "class06": "물리Ⅰ(이진*)",
                "class07": "확률과통계(김소*)",
                "class08": null,
                "class09": null,
                "class10": null
            },
            {
                "date": "2018.08.14",
                "day": "화요일",
                "class01": "스포츠문화(박용*)",
                "class02": "진로독서(오지*)",
                "class03": "심화영어(최찬*)",
                "class04": "확률과통계(김소*)",
                "class05": "화학Ⅰ(김다*)",
                "class06": "독서와문법(변미*)",
                "class07": "미적분Ⅱ(김병*)",
                "class08": null,
                "class09": null,
                "class10": null
            }, //...
        ]
    }
}
```

## Developer
Juneyoung Kang [juneyoungdev@gmail.com](mailto:juneyoungdev@gmail.com)

## LICENSE
Released under the GNU General Public License v3.