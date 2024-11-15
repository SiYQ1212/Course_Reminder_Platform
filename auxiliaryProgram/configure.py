# -*- coding: utf-8 -*-
#!/usr/bin/python3

import datetime
import os

# 开学日期 firstDay
DATA = datetime.date(2024, 8, 26)

if os.name == "posix":
    Path = "/var/www/html/"   
elif os.name == "nt":
    Path = "C:/Users/dpdgp/Desktop/WWW/xico"

# 发送时间
Send_Time = "17:30"

Struct_front = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <div class="container">
        <h1>📚 课程表</h1>
        <div class="schedule-box">
            <div class="course-list">
"""

Struct_tail = """
            </div>
        </div>
    </div>
</body>
"""

# 中文星期转换为英文
CtoE = {
    "星期一": "Monday",
    "星期二": "Tuesday",
    "星期三": "Wednesday",
    "星期四": "Thursday",
    "星期五": "Friday",
    "星期六": "Saturday",
    "星期日": "Sunday"
}

# 课时转换
TIME = {
    "1-2": "8:00-9:30",
    "3-4": "9:45-11:15",
    "3-5": "9:45-12:10",
    "6-7": "14:00-15:30",
    "8-9": "15:45-17:15",
    "8-10": "15:45-18:10",
    "11-12": "19:00-20:30",
    "11-13": "19:00-21:25"
}


