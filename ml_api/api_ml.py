from flask import Flask, request, jsonify
import joblib
import numpy as np

app = Flask(__name__)

# Load model
model = joblib.load('model_siswa.pkl')
scaler = joblib.load('scaler_siswa.pkl')
label_encoder = joblib.load('label_encoder.pkl')

@app.route('/prediksi', methods=['POST'])
def prediksi():

    data = request.json

    kehadiran = float(data['kehadiran'])
    terlambat = float(data['terlambat'])
    pelanggaran = float(data['pelanggaran'])
    sikap = float(data['sikap'])

    fitur = np.array([[kehadiran, terlambat, pelanggaran, sikap]])

    fitur_scaled = scaler.transform(fitur)

    hasil = model.predict(fitur_scaled)

    kategori = label_encoder.inverse_transform(hasil)

    return jsonify({
        'prediksi': kategori[0]
    })

if __name__ == '__main__':
    app.run(debug=True)