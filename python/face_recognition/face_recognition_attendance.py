import cv2   
import face_recognition  
import numpy as np 
import os  
import sys  
  
def load_known_faces(known_faces_dir):  
    known_face_encodings = []  
    known_face_names = []  
  
    # Memuat gambar siswa dari folder  
    for filename in os.listdir(known_faces_dir):  
        if filename.endswith(('.jpg', '.png', '.jpeg')):  
            image_path = os.path.join(known_faces_dir, filename)  
            image = face_recognition.load_image_file(image_path)  
            encoding = face_recognition.face_encodings(image)[0]  
            known_face_encodings.append(encoding)  
            known_face_names.append(os.path.splitext(filename)[0])  # Gunakan nama file sebagai nama  
  
    return known_face_encodings, known_face_names  
  
def recognize_from_camera(known_faces_dir):  
    known_face_encodings, known_face_names = load_known_faces(known_faces_dir)  
  
    # Inisialisasi kamera  
    video_capture = cv2.VideoCapture(0)  
  
    while True:  
        # Tangkap frame  
        ret, frame = video_capture.read()  
        rgb_frame = frame[:, :, ::-1]  # Konversi BGR ke RGB  
  
        # Temukan lokasi wajah dan encoding di frame saat ini  
        face_locations = face_recognition.face_locations(rgb_frame)  
        face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)  
  
        for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):  
            matches = face_recognition.compare_faces(known_face_encodings, face_encoding)  
            name = "Unknown"  
  
            # Gunakan wajah yang dikenal dengan jarak terkecil ke wajah baru  
            face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)  
            best_match_index = np.argmin(face_distances)  
            if matches[best_match_index]:  
                name = known_face_names[best_match_index]  
                print(f"Recognized: {name} - Absen Berhasil!")  # Output yang diharapkan  
                video_capture.release()  
                cv2.destroyAllWindows()  
                return "Recognized"  # Kembalikan hasil jika wajah dikenali  
  
            # Gambar kotak di sekitar wajah  
            cv2.rectangle(frame, (left, top), (right, bottom), (0, 255, 0), 2)  
            cv2.putText(frame, name, (left, top - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.75, (255, 255, 255), 2)  
  
        # Tampilkan frame yang dihasilkan  
        cv2.imshow('Video', frame)  
  
        # Hentikan loop jika tombol 'q' ditekan  
        if cv2.waitKey(1) & 0xFF == ord('q'):  
            break  
  
    # Lepaskan kamera dan tutup jendela  
    video_capture.release()  
    cv2.destroyAllWindows()  
    return "Not Recognized"  # Kembalikan hasil jika tidak ada wajah yang dikenali  
  
if __name__ == "__main__":  
    if len(sys.argv) < 2:  
        print("Usage: python face_recognition_attendance.py <known_faces_directory>")  
        sys.exit(1)  
  
    known_faces_dir = sys.argv[1]  
    recognize_from_camera(known_faces_dir)  
