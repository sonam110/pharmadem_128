import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.metrics import r2_score, mean_squared_error
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import PolynomialFeatures
from sklearn.pipeline import make_pipeline
import matplotlib.pyplot as plt
import json
# Load data from Excel file
file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/experimental_data.xlsx'  # Update the path if necessary
df = pd.read_excel(file_path, names=['Predicted', 'Experimental'], skiprows=1)

# Optionally load new predictions from an external file
new_pred_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/ORMN_new_predictions.xlsx'
new_predictions_df = pd.read_excel(new_pred_path)

# Define bin edges for groups
bins = [0, 10, 50, 100, 500, 1000]

def categorize_data(data, bins):
    categories = pd.cut(data, bins=bins, labels=[1, 2, 3, 4, 5], include_lowest=True)
    return categories

df['Predicted_Group'] = categorize_data(df['Predicted'], bins)
df['Experimental_Group'] = categorize_data(df['Experimental'], bins)

df['Fold_Difference'] = df.apply(lambda x: max(x['Predicted']/x['Experimental'], x['Experimental']/x['Predicted']), axis=1)
df['Value_Difference'] = abs(df['Predicted'] - df['Experimental'])

group_outliers = (abs(df['Predicted_Group'].cat.codes - df['Experimental_Group'].cat.codes) > 2)
value_outliers = ((df['Predicted'] > 20) & (df['Fold_Difference'] > 2)) | ((df['Predicted'] <= 20) & (df['Value_Difference'] > 10))
total_outliers = group_outliers | value_outliers

outliers = df[total_outliers]
df = df.drop(df[total_outliers].index)

X = df[['Predicted']]
y = df['Experimental']
X_train, X_test, y_train, y_test = train_test_split(X, y, train_size=0.7, random_state=42)

top_models = []

for degree in range(1, 4):
    model = make_pipeline(PolynomialFeatures(degree), LinearRegression())
    model.fit(X_train, y_train)
    y_train_pred = model.predict(X_train)
    y_test_pred = model.predict(X_test)
    train_r2 = r2_score(y_train, y_train_pred)
    test_r2 = r2_score(y_test, y_test_pred)
    rmse = np.sqrt(mean_squared_error(y_test, y_test_pred))

    top_models.append({
        'degree': degree,
        'model': model,
        'train_r2': train_r2,
        'test_r2': test_r2,
        'rmse': rmse,
        'equation': f'y = {model.named_steps["linearregression"].coef_[1]:.4f}x + {model.named_steps["linearregression"].intercept_:.4f}'
    })

# Select the best model based on test R^2
best_model_info = sorted(top_models, key=lambda x: x['test_r2'], reverse=True)[0]
best_model = best_model_info['model']

# Apply the best model to new prediction values
new_pred_corrected = best_model.predict(new_predictions_df[['Predicted']])
new_predictions_df['Corrected Predicted'] = new_pred_corrected


# data_str = np.array2string(new_predictions_df, separator=' ', precision=10, suppress_small=True)

# # Remove brackets and split the string by spaces
# numbers_str = data_str.strip('[]').split()

# # Join elements with commas where there are spaces
# formatted_data = ", ".join(numbers_str)

# # Convert to JSON
# json_data = json.dumps(formatted_data)

print(new_predictions_df)

print("Completed model evaluations and outputs.")
