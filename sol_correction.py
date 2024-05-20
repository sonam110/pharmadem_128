
import numpy as np
import json

from scipy.optimize import curve_fit

# Assuming your experimental data is in column A and predicted data in column B of an Excel sheet
# You can use libraries like pandas to read Excel files

# Example data (replace with your actual data)
experimental_data = [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 3, 3, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5] 
predicted_data = [352.366875, 271.08725, 352.366875, 0.282625, 0.581875, 1.446375, 1362.2525, 893.32775, 1362.2525, 325.700375, 246.14975, 325.700375, 0.23275, 0.482125, 1.197, 810.00325, 558.50025, 810.00325, 0.1995, 0.43225, 1.1305, 39758.637625, 19564.250125, 39758.637625, 394.760625, 300.9125, 394.760625, 0.182875, 0.382375, 1.014125, 1312.2445, 889.3045, 1312.2445, 546.713125, 405.932625, 546.713125, 7605.073, 3828.63775, 7605.073, 13079.38625, 7216.9125, 13079.38625, 1140.857375, 752.530625, 1140.857375, 1689.46575, 966.9765, 1689.46575, 167.14775, 143.623375, 167.14775, 146.3665, 135.942625, 146.3665, 2.909375, 3.89025, 6.08475, 294.22925, 228.9595, 294.22925, 2847.43025, 1809.83075, 2847.43025, 6.999125, 9.526125, 14.4305, 18.78625, 19.068875, 20.033125, 0.282625, 0.56525, 1.42975, 0.847875, 1.69575, 4.172875, 0.016625, 0.03325, 0.133, 5570.887875, 3258.749375, 5570.887875, 629.073375, 387.312625, 629.073375, 3234.826, 1826.206375, 3234.826, 173.3655, 150.72225, 173.3655, 43251.583375, 21673.2145, 43251.583375, 300.995625, 234.84475, 300.995625, 41750.59525, 21516.989375, 41750.59525, 2409.99325, 1574.454, 2409.99325, 447.195875, 344.519875, 447.195875, 797.90025, 588.658, 797.90025, 46.699625, 45.38625, 46.699625, 230.4225, 188.776875, 230.4225, 266.016625, 212.883125, 266.016625, 0.714875, 0.781375, 0.931, 0.016625, 0.03325, 0.133, 235.70925, 185.6015, 235.70925, 575.208375, 420.413, 575.208375, 0.16625, 0.3325, 0.914375, 388.45975, 312.500125, 388.45975, 1806.23975, 993.260625, 1806.23975, 844.766125, 635.74, 844.766125, 0.016625, 0.03325, 0.133, 77134.38075, 33216.367625, 77134.38075, 480.928, 352.333625, 480.928, 0.016625, 0.03325, 0.133, 190072.511125, 79219.637875, 190072.511125, 106659.233625, 46918.393375, 106659.233625, 288.294125, 216.75675, 288.294125, 0.282625, 0.56525, 1.42975, 287.230125, 232.18475, 287.230125, 4879.387625, 3050.222, 4879.387625, 164.02225, 141.229375, 164.02225, 218.1865, 190.95475, 218.1865, 27520.4265, 11780.24225, 27520.4265, 1643.198375, 1069.9185, 1643.198375, 372.10075, 279.06725, 372.10075, 560.02975, 407.129625, 560.02975, 139895.651, 58823.14025, 139895.651, 9779.2905, 5081.83025, 9779.2905, 0.249375, 0.515375, 1.346625, 100279.4065, 25207.25725, 100279.4065]

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