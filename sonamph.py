import numpy as np
import json
#FaSSGF_pH_1.6

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/UEPB/COSMO_TZVPD/UEPB_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_taurocholate/COSMO_TZVPD/Sodium_taurocholate_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Lecithin/COSMO_TZVPD/Lecithin_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Pepsin/COSMO_TZVPD/Pepsin_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Sodium_chloride/COSMO_TZVPD/Sodium_chloride_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/Hydrochloric_acid/COSMO_TZVPD/Hydrochloric_acid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/pH_Buffers/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.00055, 0.00035, 0.001, 0.0035, 0.0046, 0.99])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.00055, 0.00035, 0.001, 0.0035, 0.0046, 0.99])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.00055, 0.00035, 0.001, 0.0035, 0.0046, 0.99])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.00055, 0.00035, 0.001, 0.0035, 0.0046, 0.99])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
#print('Total logarithmic activity coefficient: ', results['tot']['lng'])
#print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
#print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])

raw_data = results['tot']['lng']

# Define keys for each data set
keys = ['data1', 'data2', 'data3', 'data4', 'data5']

# Convert to proper Python array
array_data = np.array(raw_data)

# Convert to JSON with keys and values
json_data = json.dumps(dict(zip(keys, array_data.tolist())))

print(json_data)