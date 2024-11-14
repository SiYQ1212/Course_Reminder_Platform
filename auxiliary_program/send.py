# -*- coding: utf-8 -*-
#!/usr/bin/python3
import sys
from function import *
from css import CSS1

def send_html_email(message, receiver, css="""""", subject="明日课程"):
    new_message = MIMEMultipart()
    html_part = MIMEText(message, 'html', 'utf-8')
    new_message.attach(html_part)
    css_part = MIMEText(css, 'html', 'utf-8')
    new_message.attach(css_part)
    
    proxy = get_proxy()
    if not proxy:
        log_error(f"{datetime.datetime.now()} Email Proxy Pool Empty")
        return False
    sender = choice(list(proxy.items())) 
    new_message['From'] = Header(f"LingYun <{sender[0]}>")
    new_message['To'] = Header(f"Receiver <{receiver}>")
    new_message['Subject'] = Header(subject, 'utf-8')
    server = smtplib.SMTP_SSL("smtp.qq.com", 465)
    try:
        server.login(sender[0], sender[1])
    except Exception as e:
        log_error(f"{datetime.datetime.now()}: <{inspect.getsourcefile(send_html_email)}> [send_html_email] :{sender[0]} don't match the password")
        proxy.pop(sender[0])
        with open(os.path.join(Path, "json_data", "proxy_pool.json"), "w", encoding='utf-8') as f:
            json.dump(proxy, f, ensure_ascii=False, indent=4)
        return send_html_email(message, receiver)

    try:
        server.sendmail(sender[0], receiver, new_message.as_string())
    except Exception as e:
        log_error(f"{datetime.datetime.now()}: <{inspect.getsourcefile(send_html_email)}> [send_html_email] :{e}")
        server.close()
        return False
    server.close()
    log_send(f"{datetime.datetime.now()} send successful to {receiver}")
    return True


@find_error
def send_test(receiver):
    """
    发送测试邮件
    receiver: 接收者邮箱
    """
    mapping = get_mapping()
    if mapping.get(receiver, False):
        course_information = get_tomorrow_course(receiver)
        print(course_information)
        if course_information:
            html = create_html(course_information)
            send_html_email(html, receiver, css=CSS1, subject="明日课程邮件")
        else: # 明天没有课程安排
            html = open(os.path.join(Path, "test_html", "no_course.html"), "r", encoding='utf-8').read()
            send_html_email(html, receiver, subject="无课测试邮件")
    else: # 不在课程表接收列表中
        html = open(os.path.join(Path, "test_html", "no_mapping.html"), "r", encoding='utf-8').read()
        send_html_email(html, receiver, subject="无权限测试邮件")

if __name__ == '__main__':
    try:
        send_test("2668733873@qq.com")
    except:
        print("Error")
