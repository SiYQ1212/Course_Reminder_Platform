# -*- coding: utf-8 -*-
#!/usr/bin/python3
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
import time
import datetime
import os
from functionTools import solve_pdf, log_uploads

class FolderMonitor(FileSystemEventHandler):
    def on_created(self, event):
        if not event.is_directory:
            file_name = os.path.basename(event.src_path)
            log_uploads(f"{file_name} has been created on {datetime.datetime.now()}")
            solve_pdf(file_name.split('.')[0])
    
    def on_modified(self, event):
        if not event.is_directory:
            file_name = os.path.basename(event.src_path)
            log_uploads(f"{file_name} has been modified on {datetime.datetime.now()}")
            solve_pdf(file_name.split('.')[0])
    
    def on_deleted(self, event):
        if not event.is_directory:
            file_name = os.path.basename(event.src_path)
            log_uploads(f"{file_name} has been deleted on {datetime.datetime.now()}")

def monitor_folder(path):
    if not os.path.exists(path):
        os.makedirs(path)

    event_handler = FolderMonitor()
    observer = Observer()
    observer.schedule(event_handler, path, recursive=False)

    log_uploads(f"Begin to watch: <{path}> on {datetime.datetime.now()}")
    observer.start()
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
        log_uploads(f"Watch exit on {datetime.datetime.now()}\n")
    
    observer.join()

if __name__ == "__main__":
    uploads_path = os.path.join(os.getcwd(), "uploads")
    monitor_folder(uploads_path)