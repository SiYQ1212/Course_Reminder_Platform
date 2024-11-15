# -*- coding: utf-8 -*-
# !/usr/bin/python3
from send import send_html_email
import schedule
from function import *
import time
import datetime
from configure import Path
from css import CSS1
import os
import threading
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler

class FolderMonitor(FileSystemEventHandler):
    def __init__(self, target_file):
        super().__init__()
        self.last_event_time = {}
        self.ignore_patterns = ['~', '.tmp', '.swp', '.swx']
        self.target_file = os.path.abspath(target_file) 

    def is_target_file(self, file_path):
        return os.path.abspath(file_path) == self.target_file

    def on_modified(self, event):
        if not event.is_directory and self.is_target_file(event.src_path):
            if self.debounce(event.src_path, "modified"):
                schedule_email()

    def debounce(self, file_name, event_type, threshold=1.0):
        current_time = time.time()
        last_time = self.last_event_time.get((file_name, event_type), 0)
        if current_time - last_time < threshold:
            return False
        self.last_event_time[(file_name, event_type)] = current_time
        return True

def watch_file(file_path):
    log_uploads(f"{datetime.datetime.now()} Task: watch_file have runing")
    event_handler = FolderMonitor(file_path)
    observer = Observer()
    directory = Path + "json_data/mapping.json"
    observer.schedule(event_handler, directory, recursive=False)
    observer.start()
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
    observer.join()

def schedule_email():
    schedule.clear()
    email_schedule = get_mapping()
    if email_schedule:
        for email, schedule_time in email_schedule.items():
            message = get_tomorrow_course(email)
            if message:
                html = create_html(message)
                schedule.every().day.at(schedule_time).do(send_html_email, html, email, css=CSS1)
        log_task(str(schedule.get_jobs())[:40])

def run_schedule():
    log_uploads(f"{datetime.datetime.now()} Task: run_schedule have runing")
    schedule_email()
    while True:
        schedule.run_pending()
        time.sleep(1)


if __name__ == '__main__':
    ...