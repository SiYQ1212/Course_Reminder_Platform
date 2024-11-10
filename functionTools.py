# -*- coding: utf-8 -*-
#!/usr/bin/python3
import datetime
import smtplib
from email.mime.text import MIMEText
from email.header import Header
from random import choice
import schedule
import fitz
import json
import re
import os
from Configure import *

def log_error(string):
    """
    记录错误日志
    :param string: 日志信息
    :return:
    """
    filePath = os.path.join(os.getcwd(), 'logs', 'error.log')
    with open(filePath, "a+", encoding='utf-8') as f:
        f.write(string + '\n')

def log_send(string):
    """
    记录发送日志
    :param string: 日志消息
    :return:
    """
    filePath = os.path.join(os.getcwd(), 'logs', 'send.log')
    with open(filePath, "a+", encoding='utf-8') as f:
        f.write(string + '\n')

def log_uploads(string):
    """
    记录pdf的增删改
    :param string:
    :return:
    """
    filePath = os.path.join(os.getcwd(), 'logs', 'uploads.log')
    with open(filePath, "a+", encoding='utf-8') as f:
        f.write(string + '\n')

def find_error(function):
    """
    函数错误捕捉器
    :param function: 检测函数
    :return:
    """
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
    """
    pdf_name not include .pdf
    处理pdf为json数据
    :param pdf_name: pdf文件名
    :return:
    """
    pdf_path = os.path.join(os.getcwd(), 'uploads', pdf_name + '.pdf')
    pdf = fitz.open(pdf_path)
    contents = ""
    for page in pdf:
        contents += page.get_text()

    course_information = contents.split('\n')[3:-3]
    course_information = course_information
    # 暂时存放课程信息
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

    filePath = os.path.join(os.getcwd(), 'pdf_json', pdf_name + ".json")
    with open(filePath, "w", encoding="utf-8") as f:
        json.dump(timetable, f, ensure_ascii=False, indent=4)
    return True

@find_error
def get_mapping():
    """
    获取邮箱映射表
    :return:
    """
    filePath = os.path.join(os.getcwd(), "mapping.json")
    with open(filePath, "r", encoding='utf-8') as f:
        receiver = json.load(f)
    return receiver

@find_error
def get_proxy():
    """
    获取邮箱代理池
    :return:
    """
    filePath = os.path.join(os.getcwd(), "proxy_pool.json")
    with open(filePath, "r", encoding='utf-8') as f:
        proxy = json.load(f)
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
    :return:
    """
    # 获取总课表
    filePath = os.path.join(os.getcwd(), "pdf_json", file_name + ".json")
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
    send = ["%s\n%s\n%s\n%s" % (
        TIME[infor[0]], infor[2]['地点'], infor[2]['教师'], infor[1]
    ) for infor in send_information]
    return "\n\n\n".join(send)

def send_email(message, receiver):
    """
    发送邮件功能
    :param message:
    :param receiver:
    :return:
    """
    new_message = MIMEText(message, 'plain', 'utf-8')
    proxy = get_proxy()
    if not proxy:
        log_error(f"{datetime.datetime.now()} Email Proxy Pool Empty")
        return False
    sender = choice(list(proxy.items())) # 格式为 (邮箱, 密码)
    new_message['From'] = Header(f"LingYun <{sender[0]}>")
    new_message['To'] = Header(f"Receiver <{receiver}>")
    subject = "明日课表"
    new_message['Subject'] = Header(subject, 'utf-8')
    server = smtplib.SMTP_SSL("smtp.qq.com", 465)
    try:
        server.login(sender[0], sender[1])
    except Exception as e:
        log_error(f"{datetime.datetime.now()}: <{inspect.getsourcefile(send_email)}> [send_email] :{sender[0]} don't match the password")
        proxy.pop(sender[0])
        with open(os.path.join(os.getcwd(), "proxy_pool.json"), "w", encoding='utf-8') as f:
            json.dump(proxy, f, ensure_ascii=False, indent=4)
        return send_email(message, receiver)

    try:
        server.sendmail(sender[0], receiver, new_message.as_string())
    except Exception as e:
        log_error(f"{datetime.datetime.now()}: <{inspect.getsourcefile(send_email)}> [send_email] :{e}")
        server.close()
        return False
    server.close()
    log_send(f"{datetime.datetime.now()} send successful to {receiver}")
    return True

if __name__ == '__main__':
    ...
