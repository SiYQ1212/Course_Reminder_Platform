# -*- coding: utf-8 -*-
#!/usr/bin/python3
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.header import Header
from random import choice
import fitz
import json
import re
import os
import inspect
from configure import *

def log_error(string):
    filePath = os.path.join(Path, 'logs', 'error.log')
    with open(filePath, "a+", encoding='utf-8') as f:
        f.write(string + '\n')

def log_send(string):
    filePath = os.path.join(Path, 'logs', 'send.log')
    with open(filePath, "a+", encoding='utf-8') as f:
        f.write(string + '\n')

def log_uploads(string):
    filePath = os.path.join(Path, 'logs', 'uploads.log')
    with open(filePath, "a+", encoding='utf-8') as f:
        f.write(string + '\n')

def log_task(string):
    filePath = os.path.join(Path, 'logs', 'task.log')
    with open(filePath, "a+", encoding='utf-8') as f:
        f.write(string + '\n')
        
def find_error(function):
    def wrapper(*args, **kwargs):
        try:
            result = function(*args, **kwargs)
            return result
        except Exception as e:
            log_error(f"{datetime.datetime.now()}: <{inspect.getsourcefile(function)}> [{function.__name__}] :{e}")
            return None
    return wrapper

@find_error
def solve_pdf(pdf_name):
    # pdf_name not include .pdf
    pdf_path = os.path.join(Path, 'uploadFiles', pdf_name + '.pdf')
    pdf = fitz.open(pdf_path)
    contents = ""
    for page in pdf:
        contents += page.get_text()

    course_information = contents.split('\n')[3:-3]
    course_information = course_information
    timetable = {
        "Monday": [],
        "Tuesday": [],
        "Wednesday": [],
        "Thursday": [],
        "Friday": [],
        "Saturday": [],
        "Sunday": []
    }
    PAT = re.compile(r"\d+-\d+")
    WEEK = CtoE[course_information[0]]
    CLASSHOUR = ""

    while len(course_information) >= 3:
        week = course_information[0]
        # 判断week格式是否为 "星期X"
        if CtoE.get(week) in timetable:
            WEEK = CtoE[course_information.pop(0)]
        class_hour = course_information[0]
        # 判断class_hour格式是否为 "X-X"
        if PAT.match(class_hour):
            CLASSHOUR = course_information.pop(0)
        courseName, detailedInformation, wasteInfomation = course_information.pop(0), course_information.pop(0), course_information.pop(0)
        if "学分" not in wasteInfomation:
            course_information.pop(0)

        newDetailedInformation = {}
        detailedInformation = detailedInformation.split()[:6]
        for key in range(0, len(detailedInformation), 2):
            newDetailedInformation[detailedInformation[key][:-1]] = detailedInformation[key + 1]
        timetable[WEEK].append([CLASSHOUR, courseName, newDetailedInformation])

    filePath = os.path.join(Path, 'parsingData', pdf_name + ".json")
    with open(filePath, "w", encoding="utf-8") as f:
        json.dump(timetable, f, ensure_ascii=False, indent=4)
    return True

def get_mapping():
    filePath = os.path.join(Path, "configurationInfo", "emailTimeTable.json")
    try:
        with open(filePath, "r", encoding='utf-8') as f:
            receiver = json.load(f)
    except:
        return {}
    return receiver

def get_proxy():
    filePath = os.path.join(Path, "configurationInfo", "proxyEmail.json")
    try:
        with open(filePath, "r", encoding='utf-8') as f:
            proxy = json.load(f)
    except:
        return {}
    return proxy

@find_error
def check_course(course_information, tomorrow_week):
    """
    对总明日总课表和明日周数进行筛选
    :param course_information:
    :param tomorrow_week:
    :return:
    """
    haveWeek = set()
    solveDate = course_information.split(',')
    for week in solveDate:
        if '-' in week:
            if '单' in week:
                s, e = map(int, week[:-4].split('-'))
                for i in range(s, e + 1, 2):
                    haveWeek.add(i)
            elif '双' in week:
                s, e = map(int, week[:-4].split('-'))
                for i in range(s, e + 1, 2):
                    haveWeek.add(i)
            else:
                s, e = map(int, week[:-1].split('-'))
                for i in range(s, e + 1):
                    haveWeek.add(i)
        else:
            haveWeek.add(int(week[:-1]))
    return tomorrow_week in haveWeek

@find_error
def get_tomorrow_course(file_name):
    """
    获取明日课表
    :param file_name: json文件名
    :return: 明日课表
    """
    # 获取总课表
    filePath = os.path.join(Path, "parsingData", file_name + ".json")
    with open(filePath, "r", encoding='utf-8') as f:
        data = json.load(f)

    # 获取明日总课表
    today = (datetime.datetime.now() + datetime.timedelta(days=0)).strftime("%A")
    days = list(data.keys())
    tomorrow_index = (days.index(today) + 1) % len(days)
    tomorrow = days[tomorrow_index]
    data = data[tomorrow]

    # 获取明日周数
    tomorrow_week = (datetime.date.today() + datetime.timedelta(days=1) - DATA).days // 7 + 1

    # 筛选出明日实际课表
    send_information = []
    for course in data:
        if check_course(course[2]['周数'], tomorrow_week):
            send_information.append(course)
    return send_information

@find_error
def create_html(course_information):
    """
    创建html
    :param course_information:
    :return:
    """
    html = Struct_front
    for course in course_information:
        html += f"""
                <div class="course-box">
                    <div class="time">⏰ {TIME[course[0]]}</div>
                    <div class="course-content">
                        <div class="course-name">{course[1]}</div>
                        <div class="location">🏫 {course[2]['地点'].replace('，', ',')} - {course[2]['教师']}</div>
                    </div>
                </div>
"""
    html += Struct_tail
    return html

if __name__ == '__main__':
    ...