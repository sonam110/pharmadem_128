import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.metrics import r2_score
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import PolynomialFeatures
from sklearn.pipeline import make_pipeline
import matplotlib.pyplot as plt

# Load data from Excel file

file_path = 'experimental_data.xlsx'  # Update the path if necessary
df = pd.read_excel(file_path, names=['Predicted', 'Experimental'], skiprows=1)

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

outliers_to_remove = total_outliers.sum()
total_data = len(df)
max_outliers = total_data - int(0.7 * total_data)  
outlier_indices = df[total_outliers].nlargest(max_outliers, 'Value_Difference').index
df = df.drop(outlier_indices)

X = df[['Predicted']]
y = df['Experimental']
X_train, X_test, y_train, y_test = train_test_split(X, y, train_size=0.7, random_state=42)

top_models = []

for degree in range(1, 4):
    model = make_pipeline(PolynomialFeatures(degree), LinearRegression())
    model.fit(X_train, y_train)
    y_train_pred = model.predict(X_train)
    train_r2 = r2_score(y_train, y_train_pred)
    y_test_pred = model.predict(X_test)
    test_r2 = r2_score(y_test, y_test_pred)
    
    top_models.append({
        'degree': degree,
        'train_r2': train_r2,
        'test_r2': test_r2,
        'equation': f'y = {model.named_steps["linearregression"].coef_[1]:.4f}x + {model.named_steps["linearregression"].intercept_:.4f}',
        'model': model
    })

# Sort and select the top 3 models by test R^2
top_models = sorted(top_models, key=lambda x: x['test_r2'], reverse=True)[:3]

# Evaluate each model on the entire dataset
for model_info in top_models:
    model = model_info['model']
    y_full_pred = model.predict(X)
    full_dataset_r2 = r2_score(y, y_full_pred)
    model_info['full_dataset_r2'] = full_dataset_r2

    # Visualization
    plt.figure(figsize=(10, 5))
    plt.scatter(y, y_full_pred, color='blue', label='Corrected Predictions')
    plt.scatter(y, df['Predicted'], color='red', label='Original Predictions')
    plt.title(f'Model Degree {model_info["degree"]} Performance: Train R^2 = {model_info["train_r2"]:.4f}, Test R^2 = {model_info["test_r2"]:.4f}')
    plt.xlabel('Experimental Values')
    plt.ylabel('Predicted/Corrected Predicted Values')
    plt.legend()
    plt.annotate(model_info['equation'], xy=(0.05, 0.95), xycoords='axes fraction', fontsize=12, backgroundcolor='white')
    plt.show()

# Output top models and their details
print("Top 3 Models:")
for model in top_models:
    print(f"Degree: {model['degree']}, Train R^2: {model['train_r2']:.4f}, Test R^2: {model['test_r2']:.4f}, Full Dataset R^2: {model['full_dataset_r2']:.4f}")
    print(f"Equation: {model['equation']}")

# Save details to Excel
with pd.ExcelWriter('model_performance_summary.xlsx') as writer:
    for i, model in enumerate(top_models):
        results_df = pd.DataFrame({
            'Experimental': y,
            'Predicted': df['Predicted'],
            'Corrected Predicted': model['model'].predict(X)
        })
results_df.to_excel(writer, sheet_name=f'Model_{model["degree"]}_Results')