# carrot_and_stick
## 🥕 당근과 채찍 앱 개발
----------------------------------
🥕당근과 채찍🥕은 메이커스 라이언팀이 개발한 목표 동기 부여 앱입니다. 

목표를 효과적으로 달성할 수 있도록 도와줍니다.
1. 매일 실천하고 싶지만, 포기하기 쉬운 사소한 목표를 설정합니다. ex) 물 2L 마시기, 공복 유산소 30분
2. 실천시 해당 목표를 체크합니다. 
3. 체크 하는 날짜의 연속 일자에 따라 '컬렉션 배지🐰'가 발급됩니다. ex) 3일 연속 체크 시 배지 발급!

------------------------------------
### Directory
├── controllers                          
│   ├── GoalController.php      # 주요 기능 로직
│   ├── MainController.php      # 회원 관련 기능 로직                        
├── pdos                           
│   ├── GoalPdo.php             # 주요 기능 sql 
│   ├── UserPdo.php             # 회원 관련 기능 sql
├── drawable                        # 이미지파일
├── .gitignore                     
├── composer.json                   # 필요한 외부 라이브러리
├── * composer.lock              	 
├── index.php                       # Routing 처리, logger 생성, ...                    		
└── README.md

### 어플리케이션 ERD
![image](https://user-images.githubusercontent.com/61000200/111908678-71486300-8a9d-11eb-9597-6fae0b844b32.png)

------------------------------------
## API LIST

1. Method: GET    URI: /user
2. Method: POST   URI: /user
3. Method: PATCH  URI: /user
4. Method: POST   URI: /user/token
5. Method: GET    URI: /goal/ongoing
6. Method: GET    URI: /goal/finished
7. Method: GET    URI: /goal/checkList
8. Method: GET    URI: /goal/{goalNo}
9. Method:PATCH  URI: /goal
10. Method: POST  URI: /goal/check
11. Method: POST  URI: /collection
12. Method: GET   URI: /collection

-----------------------------------
## 주요 로직

![image](https://user-images.githubusercontent.com/61000200/111911047-259ab700-8aa7-11eb-8a81-d1a30ea0ea4b.png)

