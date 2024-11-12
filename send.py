import sys
from functionTools import *

if __name__ == "__main__":
    # print(f"Usage: python3 send.py {sys.argv[0]}")
    if len(sys.argv) > 2:
        # log_send(f"Usage: python3 send.py {sys.argv[0]}")
        send_test(sys.argv[1])
