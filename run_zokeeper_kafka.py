import tkinter as tk
from tkinter import messagebox
import subprocess
import os

def start_zookeeper():
    try:
        zookeeper_process = subprocess.Popen(
            [os.path.join('C:\\kafka\\kafka-3.7.1-src', 'bin', 'windows', 'zookeeper-server-start.bat'), 
             os.path.join('C:\\kafka\\kafka-3.7.1-src', 'config', 'zookeeper.properties')],
            stdout=subprocess.PIPE, 
            stderr=subprocess.PIPE,
            creationflags=subprocess.CREATE_NEW_CONSOLE
        )
        zookeeper_status_label.config(text="Zookeeper est actif", fg="green")
    except Exception as e:
        zookeeper_status_label.config(text="Zookeeper est désactivé", fg="red")
        messagebox.showerror("Erreur", f"Erreur lors du démarrage de Zookeeper: {e}")

def start_kafka():
    try:
        kafka_process = subprocess.Popen(
            [os.path.join('C:\\kafka\\kafka-3.7.1-src', 'bin', 'windows', 'kafka-server-start.bat'), 
             os.path.join('C:\\kafka\\kafka-3.7.1-src', 'config', 'server.properties')],
            stdout=subprocess.PIPE, 
            stderr=subprocess.PIPE,
            creationflags=subprocess.CREATE_NEW_CONSOLE
        )
        kafka_status_label.config(text="Kafka est actif", fg="green")
    except Exception as e:
        kafka_status_label.config(text="Kafka est désactivé", fg="red")
        messagebox.showerror("Erreur", f"Erreur lors du démarrage de Kafka: {e}")

app = tk.Tk()
app.title("Kafka and Zookeeper Manager")
app.geometry("400x200")

zookeeper_button = tk.Button(app, text="Démarrer Zookeeper", command=start_zookeeper)
zookeeper_button.pack(pady=10)

zookeeper_status_label = tk.Label(app, text="Zookeeper est désactivé", fg="red")
zookeeper_status_label.pack(pady=10)

kafka_button = tk.Button(app, text="Démarrer Kafka", command=start_kafka)
kafka_button.pack(pady=10)

kafka_status_label = tk.Label(app, text="Kafka est désactivé", fg="red")
kafka_status_label.pack(pady=10)

app.mainloop()
