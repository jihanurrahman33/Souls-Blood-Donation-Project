<?php $pageTitle = 'Donor Dashboard'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <h2 class="text-primary mb-2">
                        <i class="fas fa-user-circle me-2"></i>Welcome, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>!
                    </h2>
                    <p class="text-muted mb-0">
                        You have <strong><?= htmlspecialchars($user['blood_group']) ?></strong> blood type. 
                        Here are the requests that match your blood group.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Matching Requests Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-heartbeat me-2"></i>Matching Blood Requests
                    </h4>
                    <p class="mb-0 mt-2">These requests need your blood type (<?= htmlspecialchars($user['blood_group']) ?>)</p>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($matchingRequests)): ?>
                        <!-- Desktop Table View (≥992px) -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i>Requester</th>
                                            <th><i class="fas fa-tint me-1"></i>Blood Group</th>
                                            <th><i class="fas fa-map-marker-alt me-1"></i>Location</th>
                                            <th><i class="fas fa-exclamation-triangle me-1"></i>Urgency</th>
                                            <th><i class="fas fa-calendar me-1"></i>Required Date</th>
                                            <th><i class="fas fa-clock me-1"></i>Posted</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($matchingRequests as $request): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($request['requester_name']) ?></strong>
                                                    <?php if ($request['first_name'] && $request['last_name']): ?>
                                                        <br><small class="text-muted">by <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger"><?= htmlspecialchars($request['blood_group']) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($request['location']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $request['urgency'] === 'critical' ? 'danger' : ($request['urgency'] === 'high' ? 'warning' : ($request['urgency'] === 'medium' ? 'info' : 'secondary')) ?>">
                                                        <?= ucfirst($request['urgency']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($request['request_date'])) ?></td>
                                                <td><?= date('M j', strtotime($request['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <a href="<?= APP_URL ?>request/details/<?= $request['id'] ?>" class="btn btn-success btn-sm">
                                                        <i class="fas fa-heart me-1"></i>View Details & Donate
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View (≤600px) -->
                        <div class="d-lg-none">
                            <div class="row g-3 p-3">
                                <?php foreach ($matchingRequests as $request): ?>
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0"><?= htmlspecialchars($request['requester_name']) ?></h6>
                                                    <span class="badge bg-<?= $request['urgency'] === 'critical' ? 'danger' : ($request['urgency'] === 'high' ? 'warning' : ($request['urgency'] === 'medium' ? 'info' : 'secondary')) ?>">
                                                        <?= ucfirst($request['urgency']) ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="row mb-2">
                                                    <div class="col-6">
                                                        <small class="text-muted">
                                                            <i class="fas fa-tint me-1"></i>Blood Group
                                                        </small>
                                                        <br>
                                                        <span class="badge bg-danger"><?= htmlspecialchars($request['blood_group']) ?></span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>Required Date
                                                        </small>
                                                        <br>
                                                        <strong><?= date('M j, Y', strtotime($request['request_date'])) ?></strong>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>Location
                                                    </small>
                                                    <br>
                                                    <strong><?= htmlspecialchars($request['location']) ?></strong>
                                                </div>
                                                
                                                <?php if ($request['first_name'] && $request['last_name']): ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-user me-1"></i>Posted by
                                                        </small>
                                                        <br>
                                                        <strong><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="d-grid">
                                                    <a href="<?= APP_URL ?>request/details/<?= $request['id'] ?>" class="btn btn-success">
                                                        <i class="fas fa-heart me-1"></i>View Details & Donate
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Matching Requests</h5>
                            <p class="text-muted">There are currently no requests for <?= htmlspecialchars($user['blood_group']) ?> blood type.</p>
                            <p class="text-muted">Check back later or view all active requests on the <a href="<?= APP_URL ?>request">Request page</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity Section -->
    <div class="row">
        <!-- Donation History -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Your Donation History
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($userDonations)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($userDonations as $donation): ?>
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($donation['donor_name']) ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-tint me-1"></i><?= htmlspecialchars($donation['blood_group']) ?>
                                                <i class="fas fa-calendar ms-2 me-1"></i><?= date('M j, Y', strtotime($donation['donation_date'])) ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-<?= $donation['status'] === 'completed' ? 'success' : ($donation['status'] === 'approved' ? 'info' : 'warning') ?>">
                                            <?= ucfirst($donation['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-hand-holding-heart fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No donation history yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Request History -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>Your Request History
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($userRequests)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($userRequests as $request): ?>
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($request['requester_name']) ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-tint me-1"></i><?= htmlspecialchars($request['blood_group']) ?>
                                                <i class="fas fa-calendar ms-2 me-1"></i><?= date('M j, Y', strtotime($request['request_date'])) ?>
                                            </small>
                                        </div>
                                                                            <span class="badge bg-<?= $request['status'] === 'completed' ? 'success' : ($request['status'] === 'in_progress' ? 'info' : 'warning') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $request['status'])) ?>
                                    </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No request history yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="<?= APP_URL ?>donate" class="btn btn-outline-primary w-100">
                                <i class="fas fa-hand-holding-heart me-2"></i>Donate Blood
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= APP_URL ?>request" class="btn btn-outline-warning w-100">
                                <i class="fas fa-search me-2"></i>Request Blood
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= APP_URL ?>forum" class="btn btn-outline-success w-100">
                                <i class="fas fa-comments me-2"></i>Visit Forum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
