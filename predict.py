import json
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier

with open('data.json') as f:
    data = json.load(f)

features = np.array(data['features'])
labels = np.array(data['labels'])
new_grades = np.array(data['new_grades']).reshape(1, -1)

X_train, X_test, y_train, y_test = train_test_split(features, labels, test_size=0.2, random_state=42)

model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

predicted_field = model.predict(new_grades)

print(predicted_field[0])
