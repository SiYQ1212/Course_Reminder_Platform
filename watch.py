# -*- coding: utf-8 -*-
#!/usr/bin/python3
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
import time
import datetime
import os
from functionTools import *

class FolderMonitor(FileSystemEventHandler):
    def on_created(self, event):
        if not event.is_directory:
            file_name = os.path.basename(event.src_path)
            if file_name.endswith('.pdf'):
                log_uploads(f"{file_name} has been created on {datetime.datetime.now()}")
                flag = solve_pdf(file_name.replace('.pdf', ''))
                if flag == False:
                    log_error(f"{file_name} has been deleted on {datetime.datetime.now()}")
                    os.remove(event.src_path)
                    mapping = get_mapping()
                    mapping.pop(file_name.replace('.pdf', ''))
                    with open("mapping.json", "w", encoding="utf-8") as f:
                        json.dump(mapping, f, ensure_ascii=False)

    def on_modified(self, event):
        if not event.is_directory:
            file_name = os.path.basename(event.src_path)
            if file_name.endswith('.pdf'):
                log_uploads(f"{file_name} has been modified on {datetime.datetime.now()}")
                flag = solve_pdf(file_name.replace('.pdf', ''))
                if flag == False:
                    log_error(f"{file_name} has been deleted on {datetime.datetime.now()}")
                    os.remove(event.src_path)
                    mapping = get_mapping()
                    mapping.pop(file_name.replace('.pdf', ''))
                    with open("mapping.json", "w", encoding="utf-8") as f:
                        json.dump(mapping, f, ensure_ascii=False)

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
            time.sleep(2)
    except KeyboardInterrupt:
        observer.stop()
        log_uploads(f"Watch exit on {datetime.datetime.now()}\n")
    
    observer.join()

def begin_watch(folder_path):
    uploads_path = os.path.join(os.getcwd(), folder_path)
    monitor_folder(uploads_path)

if __name__ == "__main__":
    begin_watch("uploads")