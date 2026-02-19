from deepface import DeepFace

results = DeepFace.find(img_path = "unknow.jpg",
			db_path = "dataset",
			model_name = "Facenet")


print(results)
