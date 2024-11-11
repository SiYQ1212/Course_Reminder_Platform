from functionTools import *


def send_test(receiver):
    mapping = get_mapping()
    if mapping.get(receiver, False):
        course_information = get_tomorrow_course(receiver)
        if course_information:
            html = create_html(course_information)
            with open(os.path.join(os.getcwd(), "css.txt"), "r", encoding='utf-8') as f:
                css = f.read()
            send_html_email(html, receiver, css=css, subject="明日课程")
        else:
            html = open(os.path.join(os.getcwd(), "send_html/no_course.html"), "r", encoding='utf-8').read()
            send_html_email(html, receiver, subject="明日课程")
    else:
        html = open(os.path.join(os.getcwd(), "send_html/no_mapping.html"), "r", encoding='utf-8').read()
        send_html_email(html, receiver, subject="测试邮件")


if __name__ == "__main__":
    ...
