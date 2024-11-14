import sys
from function import log_error
import datetime
from send import send_test

if __name__ == "__main__":
    try:
        send_test(sys.argv[1])
    except Exception as e:
        log_error(f"{datetime.datetime.now()}: </var/www/html/auxiliary_program/send_test_email.py> [main] :{e}")
