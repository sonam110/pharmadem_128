
import numpy as np
import json

from scipy.optimize import curve_fit

# Assuming your experimental data is in column A and predicted data in column B of an Excel sheet
# You can use libraries like pandas to read Excel files

# Example data (replace with your actual data)
experimental_data = [EXP_DATA]
predicted_data = [PRE_DATA]

# Define the regression model
def linear_model(x, a, b):
    return a * x + b

# Fit the model to experimental and predicted data
popt, _ = curve_fit(linear_model, experimental_data, predicted_data)

# Extract the coefficients
a, b = popt

# Apply the equation to correct predicted data
corrected_predicted_data = [linear_model(x, a, b) for x in experimental_data]

print("Regression Equation: y =", a, "* x +", b)
print("Corrected Predicted Data:", corrected_predicted_data)

# Convert the corrected predicted data to a JSON array
json_data = json.dumps(corrected_predicted_data)

print(json_data)