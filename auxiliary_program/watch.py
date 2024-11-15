# -*- coding: utf-8 -*-
#!/usr/bin/python3
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
import time
from function import *

class FolderMonitor(FileSystemEventHandler):

    def __init__(self):
        super().__init__()
        self.last_event_time = {}
        self.ignore_patterns = ['~', '.tmp', '.swp', '.swx']
        self.rename_tracking = {}
        self.old_file_name = None

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

    def on_modified(self, event):
        if event.is_directory or self.should_ignore(event.src_path):
            return
        if self.debounce(event.src_path, "modified", threshold=1):
            file_name = os.path.basename(event.src_path)
            if file_name.endswith('.pdf'):
                if self.old_file_name:
                    log_uploads(f"{self.old_file_name} has been modified to {file_name} on {datetime.datetime.now()}")
                    self.old_file_name = None
                else:
                    log_uploads(f"{file_name} has been modified on {datetime.datetime.now()}")
                
                flag = solve_pdf(file_name.replace('.pdf', ''))
                if not flag:
                    log_error(f"Because pdf_error: {file_name} has been deleted on {datetime.datetime.now()}")
                    if os.path.isfile(Path + "uploads/" + file_name):
                        os.remove(Path + "uploads/" + file_name)
                    if os.path.isfile(Path + "uploads_json/" + file_name.replace('.pdf', '.json')):
                        os.remove(Path + "uploads_json/" + file_name.replace(".pdf", ".json"))
                    try:
                        mapping = get_mapping()
                        mapping.pop(file_name.replace('.pdf', ''))
                        with open(Path + "json_data/mapping.json", "w", encoding="utf-8") as f:
                            json.dump(mapping, f, ensure_ascii=False)
                    except:
                        pass

    def on_deleted(self, event):
        if event.is_directory or self.should_ignore(event.src_path):
            return
        file_name = os.path.basename(event.src_path)
        
        current_time = time.time()
        self.rename_tracking[event.src_path] = current_time
        self.old_file_name = file_name
        
        time.sleep(0.1)
        
        if current_time - self.rename_tracking.get(event.src_path, 0) > 0.1:
            if self.debounce(event.src_path, 'deleted'):
                if not self.old_file_name:
                    log_uploads(f"{file_name} has been deleted on {datetime.datetime.now()}")
        
        self.cleanup_tracking()

    def cleanup_tracking(self):
        current_time = time.time()
        self.rename_tracking = {k: v for k, v in self.rename_tracking.items() 
                              if current_time - v < 1.0}

def monitor_folder(path):

    event_handler = FolderMonitor()
    observer = Observer()
    observer.schedule(event_handler, path, recursive=False)

    log_uploads(f"Begin to watch: <{path}>")
    observer.start()
    try:
        while True:
            time.sleep(2)
    except KeyboardInterrupt:
        observer.stop()
        log_uploads(f"Watch exit on {datetime.datetime.now()}\n")
    observer.join()

if __name__ == "__main__":
    monitor_folder(Path + "uploads")
