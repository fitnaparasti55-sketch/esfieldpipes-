<?php
/**
 * Hydraulic Pipe Sizing & Gravity Flow Calculator - Esfield Pipe
 */
$pageTitle = "DWC Hydraulic Pipe Sizing Calculator";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill mb-2">ENGINEERING SUITE</span>
                    <h2 class="fw-black text-dark">DWC HDPE Hydraulic Sizing Calculator</h2>
                    <p class="text-muted mx-auto" style="max-width: 680px;">
                        Calculate recommended nominal inner diameter (ID) and velocity for underground gravity drainage using Manning's Equation ($n = 0.009$ for smooth virgin HDPE PE-100 bore).
                    </p>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                    <div class="card-body p-4 p-md-5">
                        <form id="pipeSizingForm">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Design Discharge (Q in Litres/sec) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-water text-primary"></i></span>
                                        <input type="number" step="0.1" class="form-control" id="calcDischarge" placeholder="e.g. 150" required value="120">
                                    </div>
                                    <small class="text-muted">Estimated peak stormwater or sewage discharge.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Bed Slope Gradient (1 in N) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">1 :</span>
                                        <input type="number" step="1" class="form-control" id="calcSlope" placeholder="e.g. 200" required value="150">
                                    </div>
                                    <small class="text-muted">Enter N (e.g. 150 for 1:150 gradient).</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Infrastructure Application *</label>
                                    <select class="form-select" id="calcAppType">
                                        <option value="sewerage" selected>Municipal Underground Sewerage</option>
                                        <option value="highway">Highway / Railway Culvert Cross-Drain (SN8)</option>
                                        <option value="stormwater">Stormwater Urban Drainage</option>
                                        <option value="telecom">Telecom / Power Cable Ducting</option>
                                    </select>
                                    <small class="text-muted">Determines recommended stiffness rating.</small>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                                        <i class="fa-solid fa-calculator me-2"></i> Compute Optimal Pipe Sizing
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Results Box -->
                        <div id="calcResultBox" class="mt-5 p-4 rounded-4 border border-primary border-opacity-25 bg-primary bg-opacity-10" style="display: none;">
                            <div class="row align-items-center g-4">
                                <div class="col-md-7">
                                    <span class="badge bg-primary text-white mb-2 px-3 py-1.5 fw-bold">RECOMMENDED SPECIFICATION</span>
                                    <h3 class="fw-black text-dark mb-3" id="resNominalDia">300 mm ID</h3>
                                    <div class="row g-2 small text-dark">
                                        <div class="col-sm-6">
                                            <strong>Discharge Velocity:</strong><br>
                                            <span id="resFlowVelocity" class="fw-semibold text-primary"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Full-Bore Flow Capacity:</strong><br>
                                            <span id="resMaxCapacity" class="fw-semibold text-success"></span>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <strong>Recommended Stiffness Class:</strong><br>
                                            <span id="resStiffnessRec" class="fw-bold text-dark"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-end">
                                    <a href="#" id="calcExploreBtn" class="btn btn-primary px-4 py-3 fw-bold shadow">
                                        <i class="fa-solid fa-boxes-stacked me-1"></i> View Sizing in Catalog &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hydraulic Comparison Reference Table -->
                <div class="card border-0 shadow-sm p-4 p-md-5">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-table me-2 text-primary"></i> DWC HDPE vs Conventional Concrete (RCC) Roughness Coefficients</h5>
                    <p class="text-muted small mb-4">Manning's 'n' value dictates friction loss. DWC HDPE ($n=0.009$) provides 35-40% higher flow discharge than concrete ($n=0.015$), allowing smaller pipe diameters at identical gradients.</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center small mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pipe Material</th>
                                    <th>Manning's Roughness (n)</th>
                                    <th>Velocity at 1:200 Slope</th>
                                    <th>Abrasion Scour Resistance</th>
                                    <th>Joint Watertightness</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-primary fw-bold">
                                    <td class="text-start"><i class="fa-solid fa-circle-check text-primary me-2"></i> Esfield DWC HDPE</td>
                                    <td>0.009 - 0.010</td>
                                    <td>1.85 m/s (High Velocity)</td>
                                    <td>Zero wear / PE-100 High Abrasion</td>
                                    <td>100% Watertight EPDM Lip Seal</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Reinforced Concrete (NP3/NP4 RCC)</td>
                                    <td>0.013 - 0.015</td>
                                    <td>1.15 m/s (Silt Deposition Risk)</td>
                                    <td>Porous, scours under sand bedload</td>
                                    <td>Mortar joint prone to root leaks</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Solid Wall PVC Pipe</td>
                                    <td>0.011</td>
                                    <td>1.55 m/s</td>
                                    <td>Brittle under dynamic road vibration</td>
                                    <td>Solvent / Rubber Gasket</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
