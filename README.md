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

### KoreanSchoolMeal
- Method: GET
- URL: http://localhost:4567/koreanschoolmeal.php
- Content-Type: application/json; charset=utf-8
- Parameters  

| Request | Field Name | Details | Type | Required | Example Value |
|---------|-------------|----------|--------|----------|---------------|
| GET | countryCode | 교육청코드 | String | Y | `stu.goe.go.kr` |
| GET | schoolCode | 학교코드 | String | Y | `J100004922` |
| GET | schoolName | 학교이름 | String | Y | `교하고등학교` |
| GET | schoolTypeCode | 학교타입코드 | String | Y | `4` |
| GET | resultType | 결과타입 | String | Y | `today`/`tomorrow`/`week`/`date` |
| GET | schoolMealDate | 결과타입==date 경우 특정일자 | String | N | `2018-08-14` |

- Example
```
curl -XGET "http://localhost:4567/koreanschoolmeal.php?countryCode=stu.goe.go.kr&schoolCode=J100004922&schoolName=교하고등학교&schoolTypeCode=4&resultType=week"

curl -XGET "http://localhost:4567/koreanschoolmeal.php?countryCode=stu.goe.go.kr&schoolCode=J100004922&schoolName=교하고등학교&schoolTypeCode=4&resultType=date&schoolMealDate=2018-05-17"
```
- Response:
```
{
    "apiName": "koreanschoolmeal",
    "data": {
        "schoolName": "교하고등학교",
        "countryCode": "stu.goe.go.kr",
        "schoolCode": "J100004922",
        "schoolType": "고등학교",
        "resultType": "week",
        "timeStamp": "2018.08.15 10:25:30",
        "result": [
            {
                "date": "2018.08.13",
                "day": "월요일",
                "breakfast": null,
                "lunch": "쌀밥(교하)짜장소스(교하)후르츠탕수육(교하)치커리사과무침(교하)배추김치(교하)팽이장국(교하)",
                "dinner": null
            },
            {
                "date": "2018.08.14",
                "day": "화요일",
                "breakfast": null,
                "lunch": "찰보리밥(교하)부대찌개(교하)돈육메추리알장조림(교하)삼치무조림(교하)총각김치(교하)수박(교하)",
                "dinner": null
            },
            {
                "date": "2018.08.15",
                "day": "수요일",
                "breakfast": null,
                "lunch": "급식이 없습니다.",
                "dinner": null
            },
            {
                "date": "2018.08.16",
                "day": "목요일",
                "breakfast": null,
                "lunch": "칼슘기장밥(교하)얼갈이된장국(교하)삼겹살오븐구이(교하)쌈장(교하)비빔막국수(교하)배추김치(교하)",
                "dinner": null
            },
            {
                "date": "2018.08.17",
                "day": "금요일",
                "breakfast": null,
                "lunch": "찰현미밥(교하)미니설렁탕(교하)칠리새우(교하)미역줄기볶음(교하)깍두기(교하)",
                "dinner": null
            }
        ]
    }
}
```

## Developer
Juneyoung Kang [juneyoungdev@gmail.com](mailto:juneyoungdev@gmail.com)

## LICENSE
Released under the GNU General Public License v3.