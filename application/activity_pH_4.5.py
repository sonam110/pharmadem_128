import numpy as np

from opencosmorspy import Parameterization, COSMORS
crs = COSMORS(par='default_orca')
crs.par.calculate_contact_statistics_molecule_properties = True
mol_structure_list_0 = ['opencosmorspy/Mesalamine_c000.orcacosmo']
crs.add_molecule(mol_structure_list_0)
crs.add_molecule(['opencosmorspy/OC_solventDB_68_new/aceticacid/COSMO_TZVPD/aceticacid_c000.orcacosmo'])
crs.add_molecule(['opencosmorspy/OC_solventDB_68_new/h2o/COSMO_TZVPD/h2o_c000.orcacosmo'])
x = np.array([0.0, 0.6, 0.4])
T = 283.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.6, 0.4])
T = 298.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.6, 0.4])
T = 323.15
crs.add_job(x, T, refst='pure_component')

x = np.array([0.0, 0.6, 0.4])
T = 353.15
crs.add_job(x, T, refst='pure_component')

results = crs.calculate()
print('Total logarithmic activity coefficient: ', results['tot']['lng'])
print('Residual logarithmic activity coefficient: ', results['enth']['lng'])
print('Combinatorial logarithmic activity coefficient:', results['comb']['lng'])