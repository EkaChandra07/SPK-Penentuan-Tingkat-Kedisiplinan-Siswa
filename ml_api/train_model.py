import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.tree import DecisionTreeClassifier
from sklearn.metrics import accuracy_score, classification_report
import joblib

# Membaca dataset
data = pd.read_csv('data_siswa_ml.csv')

# Menampilkan nama kolom
print("Kolom dataset:", list(data.columns))

# Menentukan fitur dan target
X = data[['Kehadiran', 'Terlambat', 'Pelanggaran', 'Sikap']]
y = data['Kategori']

# Encoding label
label_encoder = LabelEncoder()
y_encoded = label_encoder.fit_transform(y)

# Normalisasi data
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)

# Membagi data latih dan data uji
X_train, X_test, y_train, y_test = train_test_split(
    X_scaled,
    y_encoded,
    test_size=0.2,
    random_state=42,
    stratify=y_encoded
)

# Melatih model
model = DecisionTreeClassifier(random_state=42)
model.fit(X_train, y_train)

# Prediksi
y_pred = model.predict(X_test)

# Evaluasi akurasi
accuracy = accuracy_score(y_test, y_pred)
print(f"\nAkurasi Model: {accuracy * 100:.2f}%")

# Laporan klasifikasi
print("\nLaporan Klasifikasi:")
print(classification_report(
    y_test,
    y_pred,
    labels=range(len(label_encoder.classes_)),
    target_names=label_encoder.classes_,
    zero_division=0
))

# Menyimpan model
joblib.dump(model, 'model_siswa.pkl')
joblib.dump(scaler, 'scaler_siswa.pkl')
joblib.dump(label_encoder, 'label_encoder.pkl')

print("\nModel berhasil disimpan:")
print("- model_siswa.pkl")
print("- scaler_siswa.pkl")
print("- label_encoder.pkl")