# -*- coding: utf-8 -*-
#!/usr/bin/python3
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
import time
import os
import datetime

class FolderMonitor(FileSystemEventHandler):

    def __init__(self):
        super().__init__()
        self.last_event_time = {}
        self.ignore_patterns = ['~', '.tmp', '.swp', '.swx']
        self.pending_delete = None
        self.delete_time = None

    def should_ignore(self, file_path):
        return any(pattern in file_path for pattern in self.ignore_patterns)

    def debounce(self, file_name, event_type, threshold=1.0):
        current_time = time.time()
        last_time = self.last_event_time.get((file_name, event_type), 0)
        if current_time - last_time < threshold:
            return False
        self.last_event_time[(file_name, event_type)] = current_time
        return True

    def on_created(self, event):
        if event.is_directory or self.should_ignore(event.src_path):
            return
        
        current_time = time.time()
        file_name = os.path.basename(event.src_path)
        
        if self.pending_delete and (current_time - self.delete_time) < 0.5:
            if file_name == self.pending_delete:
                print(f"{file_name} has been modified on {datetime.datetime.now()}")
            else:
                print(f"{self.pending_delete} has been renamed to {file_name} on {datetime.datetime.now()}")
            self.pending_delete = None
            self.delete_time = None
        else:
            print(f"{file_name} has been created on {datetime.datetime.now()}")

    def on_modified(self, event):
        if event.is_directory or self.should_ignore(event.src_path):
            return
        if self.debounce(event.src_path, "modified", threshold=1):
            file_name = os.path.basename(event.src_path)
            if not self.pending_delete:
                print(f"{file_name} has been modified on {datetime.datetime.now()}")

    def on_deleted(self, event):
        if event.is_directory or self.should_ignore(event.src_path):
            return
        
        file_name = os.path.basename(event.src_path)
        self.pending_delete = file_name
        self.delete_time = time.time()
        
        time.sleep(0.2)
        
        current_time = time.time()
        if self.pending_delete == file_name and (current_time - self.delete_time) >= 0.2:
            if self.debounce(event.src_path, 'deleted'):
                print(f"{file_name} has been deleted on {datetime.datetime.now()}")
            self.pending_delete = None
            self.delete_time = None


def monitor_folder(path):

    event_handler = FolderMonitor()
    observer = Observer()
    observer.schedule(event_handler, path, recursive=False)

    print(f"Begin to watch: <{path}>")
    observer.start()
    try:
        while True:
            time.sleep(2)
    except KeyboardInterrupt:
        observer.stop()
        print(f"Watch exit on {datetime.datetime.now()}\n")
    observer.join()

if __name__ == "__main__":
    monitor_folder("C:/Users/dpdgp/Desktop/WWW/xico/uploads/")
