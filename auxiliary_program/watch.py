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
        # 添加需要忽略的文件模式
        self.ignore_patterns = ['~', '.tmp', '.swp', '.swx']

    def should_ignore(self, file_path):
        """检查是否应该忽略该文件"""
        return any(pattern in file_path for pattern in self.ignore_patterns)

    def debounce(self, file_name, event_type, threshold=1.0):
        current_time = time.time()
        last_time = self.last_event_time.get((file_name, event_type), 0)
        if current_time - last_time < threshold:
            return False
        self.last_event_time[(file_name, event_type)] = current_time
        return True

    def on_modified(self, event):
        if not event.is_directory and not self.should_ignore(event.src_path):
            if self.debounce(event.src_path, "modified"):
                file_name = os.path.basename(event.src_path)
                if file_name.endswith('.pdf'):
                    log_uploads(f"{file_name} has been modified on {datetime.datetime.now()}")
                    flag = solve_pdf(file_name.replace('.pdf', ''))
                    if not flag:
                        log_error(f"{file_name} has been deleted on {datetime.datetime.now()}")
                        if os.path.isfile(Path + "uploads/" + file_name):
                            os.remove(Path + "uploads/" + file_name)
                            os.remove(Path + "uploads_json/" + file_name.replace(".pdf", ".json"))
                        try:
                            mapping = get_mapping()
                            mapping.pop(file_name.replace('.pdf', ''))
                            with open(Path + "json_data/mapping.json", "w", encoding="utf-8") as f:
                                json.dump(mapping, f, ensure_ascii=False)
                        except:
                            pass

    def on_deleted(self, event):
        if not event.is_directory and not self.should_ignore(event.src_path)    :
            if self.debounce(event.src_path, "deleted"):
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

if __name__ == "__main__":
    monitor_folder(Path + "uploads")