import threading
import datetime
from persistent_send import run_schedule, watch_file
from watch import begin_watch
from configure import Path
from function import log_thread

def run_with_restart(target, *args):
    while True:
        try:
            log_thread(f"{datetime.datetime.now()}: {target.__name__} start")
            target(*args)
        except Exception as e:
            log_thread(f"{datetime.datetime.now()}: Error in {target.__name__}: {e}. Restarting..." + "\n\n")

def main():
    try:
        thread1 = threading.Thread(target=run_with_restart, args=(run_schedule,))
        thread2 = threading.Thread(target=run_with_restart, args=(begin_watch, Path + "uploads"))
        thread3 = threading.Thread(target=run_with_restart, args=(watch_file, Path + "json_data/mapping.json"))

        thread1.start()
        thread2.start()
        thread3.start()

        thread1.join()
        thread2.join()
        thread3.join()

    except KeyboardInterrupt:
        log_thread("Program interrupted. Exiting..." + "\n\n")

if __name__ == '__main__':
    main()

