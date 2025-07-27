<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title floating">Search Express Bus Tickets in Myanmar</h1>
            <p class="hero-subtitle">Safe, reliable, and affordable travel with E-ticket Myanmar</p>
        </div>
    </div>
</section>

<!-- Search Form -->
<div class="container search-container">
    <div class="search-wrapper">
        <div class="search-header">
            <h2 class="search-title"><i class="bi bi-search"></i> Find Your Perfect Journey</h2>
            <p class="search-subtitle">Search for available routes and book your tickets instantly</p>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="error-message">
            <i class="bi bi-exclamation-triangle"></i>
            <?= implode('<br>', $errors) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="row g-4">
                <div class="col-lg-2 col-md-6">
                    <div class="form-group">
                        <label class="form-label">Ticket Type</label>
                        <select class="form-select" name="ticket" required>
                            <option value="">Select Type</option>
                            <option value="Normal" <?= ($_POST['ticket'] ?? '') === 'Normal' ? 'selected' : '' ?>>Normal
                            </option>
                            <option value="VIP" <?= ($_POST['ticket'] ?? '') === 'VIP' ? 'selected' : '' ?>>VIP</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label class="form-label">From</label>
                        <select class="form-select" name="from" required>
                            <option value="">Select Departure</option>
                            <option value="Yangon" <?= ($_POST['from'] ?? '') === 'Yangon' ? 'selected' : '' ?>>Yangon
                                (ရန်ကုန်)</option>
                            <option value="Mandalay" <?= ($_POST['from'] ?? '') === 'Mandalay' ? 'selected' : '' ?>>
                                Mandalay (မန္တလေး)</option>
                            <option value="Taunggyi" <?= ($_POST['from'] ?? '') === 'Taunggyi' ? 'selected' : '' ?>>
                                Taunggyi (တောင်ကြီး)</option>
                            <option value="Bagan" <?= ($_POST['from'] ?? '') === 'Bagan' ? 'selected' : '' ?>>Bagan
                                (ပုဂံ)</option>
                            <option value="Naypyitaw" <?= ($_POST['from'] ?? '') === 'Naypyitaw' ? 'selected' : '' ?>>
                                Naypyitaw (နေပြည်တော်)</option>
                            <option value="Meiktila" <?= ($_POST['from'] ?? '') === 'Meiktila' ? 'selected' : '' ?>>
                                Meiktila (မိတ္ထီလာ)</option>
                            <option value="Monywa" <?= ($_POST['from'] ?? '') === 'Monywa' ? 'selected' : '' ?>>Monywa
                                (မုံရွာ)</option>
                            <option value="Bago" <?= ($_POST['from'] ?? '') === 'Bago' ? 'selected' : '' ?>>Bago (ပဲခူး)
                            </option>
                            <option value="KyaukSe" <?= ($_POST['from'] ?? '') === 'KyaukSe' ? 'selected' : '' ?>>
                                KyaukSe (ကျောက်ဆည်)</option>
                            <option value="Pyin Oo Lwin"
                                <?= ($_POST['from'] ?? '') === 'Pyin Oo Lwin' ? 'selected' : '' ?>>Pyin Oo Lwin
                                (ပြင်ဦးလင်း)</option>
                            <option value="Naung Cho" <?= ($_POST['from'] ?? '') === 'Naung Cho' ? 'selected' : '' ?>>
                                Naung Cho (နောင်ချို)</option>
                        </select>
                        <small class="text-muted">* Choose Taunggyi for Inle lake, Kalaw</small>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label class="form-label">To</label>
                        <select class="form-select" name="to" required>
                            <option value="">Select Destination</option>
                            <option value="Yangon" <?= ($_POST['to'] ?? '') === 'Yangon' ? 'selected' : '' ?>>Yangon
                                (ရန်ကုန်)</option>
                            <option value="Mandalay" <?= ($_POST['to'] ?? '') === 'Mandalay' ? 'selected' : '' ?>>
                                Mandalay (မန္တလေး)</option>
                            <option value="Taunggyi" <?= ($_POST['to'] ?? '') === 'Taunggyi' ? 'selected' : '' ?>>
                                Taunggyi (တောင်ကြီး)</option>
                            <option value="Bagan" <?= ($_POST['to'] ?? '') === 'Bagan' ? 'selected' : '' ?>>Bagan (ပုဂံ)
                            </option>
                            <option value="Naypyitaw" <?= ($_POST['to'] ?? '') === 'Naypyitaw' ? 'selected' : '' ?>>
                                Naypyitaw (နေပြည်တော်)</option>
                            <option value="Meiktila" <?= ($_POST['to'] ?? '') === 'Meiktila' ? 'selected' : '' ?>>
                                Meiktila (မိတ္ထီလာ)</option>
                            <option value="Monywa" <?= ($_POST['to'] ?? '') === 'Monywa' ? 'selected' : '' ?>>Monywa
                                (မုံရွာ)</option>
                            <option value="Bago" <?= ($_POST['to'] ?? '') === 'Bago' ? 'selected' : '' ?>>Bago (ပဲခူး)
                            </option>
                            <option value="KyaukSe" <?= ($_POST['to'] ?? '') === 'KyaukSe' ? 'selected' : '' ?>>KyaukSe
                                (ကျောက်ဆည်)</option>
                            <option value="Pyin Oo Lwin"
                                <?= ($_POST['to'] ?? '') === 'Pyin Oo Lwin' ? 'selected' : '' ?>>Pyin Oo Lwin
                                (ပြင်ဦးလင်း)</option>
                            <option value="Naung Cho" <?= ($_POST['to'] ?? '') === 'Naung Cho' ? 'selected' : '' ?>>
                                Naung Cho (နောင်ချို)</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="form-group">
                        <label class="form-label">Depart on</label>
                        <input type="date" class="form-control" name="depart" min="<?= $minDate ?>"
                            max="<?= $maxDate ?>"
                            value="<?= htmlspecialchars($_POST['depart'] ?? date('Y-m-d', strtotime('+1 day'))) ?>">
                        <small class="text-muted">Optional for demo</small>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="form-group">
                        <label class="form-label">Passenger</label>
                        <select class="form-select" name="passenger" required>
                            <option value="">Select</option>
                            <option value="1 Person"
                                <?= ($_POST['passenger'] ?? '') === '1 Person' ? 'selected' : '' ?>>1 Person</option>
                            <option value="2 Persons"
                                <?= ($_POST['passenger'] ?? '') === '2 Persons' ? 'selected' : '' ?>>2 Persons</option>
                            <option value="3 Persons"
                                <?= ($_POST['passenger'] ?? '') === '3 Persons' ? 'selected' : '' ?>>3 Persons</option>
                            <option value="4 Persons"
                                <?= ($_POST['passenger'] ?? '') === '4 Persons' ? 'selected' : '' ?>>4 Persons</option>
                            <option value="Group (Male)"
                                <?= ($_POST['passenger'] ?? '') === 'Group (Male)' ? 'selected' : '' ?>>Group (Male)
                            </option>
                            <option value="Group (Female)"
                                <?= ($_POST['passenger'] ?? '') === 'Group (Female)' ? 'selected' : '' ?>>Group (Female)
                            </option>
                            <option value="Group (Monk)"
                                <?= ($_POST['passenger'] ?? '') === 'Group (Monk)' ? 'selected' : '' ?>>Group (Monk)
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="#" class="text-decoration-none" style="color: #667eea; font-weight: 600;">
                            <i class="bi bi-question-circle"></i> FAQ ?
                        </a>
                        <div class="d-flex gap-3 align-items-center">
                            <input type="text" class="promo-input" name="promo" placeholder="Promo Code"
                                value="<?= htmlspecialchars($_POST['promo'] ?? '') ?>" style="width: 200px;">
                            <button class="search-btn" type="submit">
                                <i class="bi bi-search"></i> SEARCH
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>