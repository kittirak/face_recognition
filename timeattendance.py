import cv2
import pandas as pd
from deepface import DeepFace
from datetime import datetime

import os

# ตั้งค่าเบื้องต้น
db_path = "dataset" # โฟลเดอร์ที่เก็บรูปอ้างอิง
csv_file = "attendance.csv"

def log_attendance(name):
    # ฟังก์ชันบันทึกเวลาลง CSV
    now = datetime.now()
    date_str = now.strftime("%Y-%m-%d")
    time_str = now.strftime("%H:%M:%S")

    if not os.path.isfile(csv_file):
        df = pd.DataFrame(columns=["Name", "Date", "Time"])
        df.to_csv(csv_file, index=False)

    df = pd.read_csv(csv_file)

    # เช็คว่าวันนี้คนนี้เช็คชื่อไปหรือยัง (ป้องกันการบันทึกซ้ำรัวๆ)
    today_check = df[(df['Name'] == name) & (df['Date'] == date_str)]


    if today_check.empty:
        new_entry = pd.DataFrame([[name, date_str, time_str]], columns=["Name", "Date", "Time"])
        new_entry.to_csv(csv_file, mode='a', header=False, index=False)
        print(f"✅ บันทึกเวลา: {name} เมื่อ {time_str}")


# เปิดกล้อง
cap = cv2.VideoCapture(0)

while True:
    ret, frame = cap.read()
    if not ret: break
    try:
        # สแกนหาใบหน้าและเปรียบเทียบกับใน Database
        results = DeepFace.find(img_path=frame, 
                                db_path=db_path, 
                                model_name="Facenet", 
                                enforce_detection=False)

        if len(results) > 0 and not results[0].empty:

            # ดึงชื่อจาก Path ของไฟล์ (ชื่อโฟลเดอร์)
            path = results[0]['identity'][0]
            name = os.path.basename(path)

            # วาดกรอบและชื่อ
            source_x = results[0]['source_x'][0]
            source_y = results[0]['source_y'][0]
            source_w = results[0]['source_w'][0]
            source_h = results[0]['source_h'][0]

            cv2.rectangle(frame, (source_x, source_y), (source_x+source_w, source_y+source_h), (0, 255, 0), 2)
            cv2.putText(frame, name, (source_x, source_y - 10), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)

            # บันทึกเวลา
            log_attendance(name)

    except Exception as e:
        print(f"Error: {e}")

    cv2.imshow("Face Recognition Attendance", frame)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
