<?php
/**
 * 求人検索フォームテンプレート
 * 
 * このファイルは、求人の検索フォームを表示します。
 * front-page.php からインクルードするか、get_template_part('search-form') で呼び出します。
 */
?>

<div class="search-container">
  <div class="search-header">
    <h2 class="search-title">求人検索</h2>
    <p class="search-count">
      求人件数 <span id="job-count">
        <?php 
        // 求人数を取得
        $count_posts = wp_count_posts('job');
        echo $count_posts->publish;
        ?>
      </span>件 
      <span id="update-date"><?php echo date('Y年m月d日'); ?></span>更新
    </p>
  </div>
  
  <div class="search-content">
    <form id="job-search-form" method="get">
      <!-- メイン検索部分（エリアと職種のみ） -->
      <div class="main-search-section">
        <div class="search-row">
          <!-- エリア選択 -->
          <div class="search-col">
            <div class="search-group">
              <div class="search-label">
                <span class="label-icon"><i class="fas fa-map-marker-alt"></i></span>
                <span class="label-text">エリア</span>
              </div>
              <div class="selection-field" id="area-field">
                <div class="selection-display">
                  <span class="selection-placeholder">エリアを選択</span>
                </div>
                <input type="hidden" name="location" id="location-input" value="">
                <input type="hidden" name="location_name" id="location-name-input" value="">
                <input type="hidden" name="location_term_id" id="location-term-id-input" value="">
              </div>
            </div>
          </div>
          
          <!-- 職種選択 -->
          <div class="search-col">
            <div class="search-group">
              <div class="search-label">
                <span class="label-icon"><i class="fas fa-briefcase"></i></span>
                <span class="label-text">職種</span>
              </div>
              <div class="selection-field" id="position-field">
                <div class="selection-display">
                  <span class="selection-placeholder">職種を選択</span>
                </div>
                <input type="hidden" name="position" id="position-input" value="">
                <input type="hidden" name="position_name" id="position-name-input" value="">
                <input type="hidden" name="position_term_id" id="position-term-id-input" value="">
              </div>
            </div>
          </div>
        </div>
        
        <div class="search-actions">
          <button type="button" id="search-btn" class="search-button">検索する</button>
          <button type="button" id="detail-toggle-btn" class="detail-button">詳細を指定</button>
        </div>
      </div>
      
      <!-- 詳細検索セクション（初期状態では非表示） -->
      <div class="detail-search-section" style="display: none;">
        <div class="detail-heading-row">
          <h3 class="detail-heading">詳細条件</h3>
        </div>
        
        <div class="search-row">
          <!-- 雇用形態 -->
          <div class="search-col">
            <div class="search-group">
              <div class="search-label">
                <span class="label-icon"><i class="fas fa-building"></i></span>
                <span class="label-text">雇用形態</span>
              </div>
              <div class="selection-field" id="job-type-field">
                <div class="selection-display">
                  <span class="selection-placeholder">雇用形態を選択</span>
                </div>
                <input type="hidden" name="job_type" id="job-type-input" value="">
                <input type="hidden" name="job_type_name" id="job-type-name-input" value="">
                <input type="hidden" name="job_type_term_id" id="job-type-term-id-input" value="">
              </div>
            </div>
          </div>
          
          <!-- 施設形態 -->
          <div class="search-col">
            <div class="search-group">
              <div class="search-label">
                <span class="label-icon"><i class="fas fa-hospital"></i></span>
                <span class="label-text">施設形態</span>
              </div>
              <div class="selection-field" id="facility-type-field">
                <div class="selection-display">
                  <span class="selection-placeholder">施設形態を選択</span>
                </div>
                <input type="hidden" name="facility_type" id="facility-type-input" value="">
                <input type="hidden" name="facility_type_name" id="facility-type-name-input" value="">
                <input type="hidden" name="facility_type_term_id" id="facility-type-term-id-input" value="">
              </div>
            </div>
          </div>
        </div>
        
        <!-- 求人の特徴 -->
        <div class="feature-section">
          <h4 class="feature-heading">求人の特徴</h4>
          <div class="feature-field" id="feature-field">
            <div class="feature-selection-display">
              <span class="feature-placeholder">特徴を選択（複数選択可）</span>
            </div>
            <div class="selected-features" id="selected-features"></div>
            <input type="hidden" name="job_feature" id="job-feature-input" value="">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- エリア選択モーダル -->
<div class="modal-overlay" id="area-modal-overlay">
  <div class="modal-wrapper">
    <!-- ステップ1: トップレベルカテゴリー選択 -->
    <div class="modal-panel" id="area-selection-modal">
      <div class="modal-header">
        <h3 class="modal-title">エリアを選択</h3>
        <button type="button" class="modal-close" data-target="area-modal-overlay">&times;</button>
      </div>
      <div class="modal-body">
        <div class="area-grid">
          <?php
          // job_location タクソノミーのトップレベル（親なし）のタームを取得
          $top_areas = get_terms(array(
              'taxonomy' => 'job_location',
              'hide_empty' => false,
              'parent' => 0, // 親を持たないタームのみ
          ));
          
          if (!empty($top_areas) && !is_wp_error($top_areas)) {
              foreach ($top_areas as $area) {
                  echo '<div class="area-btn" data-term-id="' . esc_attr($area->term_id) . '" data-name="' . esc_attr($area->name) . '" data-slug="' . esc_attr($area->slug) . '">' . esc_html($area->name) . '</div>';
              }
          } else {
              echo '<p>エリアが定義されていません。管理画面でタクソノミーを設定してください。</p>';
          }
          ?>
        </div>
      </div>
    </div>
    
    <!-- ステップ2: 第2階層（都道府県など）選択 -->
    <div class="modal-panel" id="prefecture-selection-modal" style="display: none;">
      <div class="modal-header">
        <h3 class="modal-title"><span id="selected-area-name"></span>から選択</h3>
        <button type="button" class="modal-close" data-target="area-modal-overlay">&times;</button>
      </div>
      <div class="modal-body">
        <div class="prefecture-grid" id="prefecture-grid">
          <!-- 動的にロードされる第2階層のターム -->
        </div>
        
        <div class="modal-actions">
          <button type="button" class="back-btn" data-target="area-selection-modal">
            <i class="fas fa-arrow-left"></i> 戻る
          </button>
          <button type="button" class="select-area-btn" id="select-area-btn">
            <span id="selected-area-btn-name"></span>全域で検索
          </button>
        </div>
      </div>
    </div>
    
    <!-- ステップ3: 第3階層（市区町村など）選択 -->
    <div class="modal-panel" id="city-selection-modal" style="display: none;">
      <div class="modal-header">
        <h3 class="modal-title"><span id="selected-prefecture-name"></span>から選択</h3>
        <button type="button" class="modal-close" data-target="area-modal-overlay">&times;</button>
      </div>
      <div class="modal-body">
        <div id="city-grid" class="city-grid">
          <!-- 動的にロードされる第3階層のターム -->
        </div>
        
        <div class="modal-actions">
          <button type="button" class="back-btn" data-target="prefecture-selection-modal">
            <i class="fas fa-arrow-left"></i> 戻る
          </button>
          <button type="button" class="select-prefecture-btn" id="select-prefecture-btn">
            <span id="selected-prefecture-btn-name"></span>全域で検索
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 職種選択モーダル -->
<div class="modal-overlay" id="position-modal-overlay">
  <div class="modal-wrapper">
    <div class="modal-panel" id="position-selection-modal">
      <div class="modal-header">
        <h3 class="modal-title">職種を選択</h3>
        <button type="button" class="modal-close" data-target="position-modal-overlay">&times;</button>
      </div>
      <div class="modal-body">
        <div class="position-grid">
          <?php
          // job_position タクソノミーの項目を取得
          $positions = get_terms(array(
              'taxonomy' => 'job_position',
              'hide_empty' => false,
          ));
          
          if (!empty($positions) && !is_wp_error($positions)) {
              foreach ($positions as $position) {
                  echo '<div class="position-btn" data-term-id="' . esc_attr($position->term_id) . '" data-name="' . esc_attr($position->name) . '" data-slug="' . esc_attr($position->slug) . '" data-url="' . esc_url(get_term_link($position)) . '">' . esc_html($position->name) . '</div>';
              }
          } else {
              echo '<p>職種が定義されていません。管理画面でタクソノミーを設定してください。</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 雇用形態選択モーダル -->
<div class="modal-overlay" id="job-type-modal-overlay">
  <div class="modal-wrapper">
    <div class="modal-panel" id="job-type-selection-modal">
      <div class="modal-header">
        <h3 class="modal-title">雇用形態を選択</h3>
        <button type="button" class="modal-close" data-target="job-type-modal-overlay">&times;</button>
      </div>
      <div class="modal-body">
        <div class="selection-grid">
          <?php
          // job_type タクソノミーの項目を取得
          $job_types = get_terms(array(
              'taxonomy' => 'job_type',
              'hide_empty' => false,
          ));
          
          if (!empty($job_types) && !is_wp_error($job_types)) {
              foreach ($job_types as $type) {
                  echo '<div class="selection-btn job-type-btn" data-term-id="' . esc_attr($type->term_id) . '" data-name="' . esc_attr($type->name) . '" data-slug="' . esc_attr($type->slug) . '" data-url="' . esc_url(get_term_link($type)) . '">' . esc_html($type->name) . '</div>';
              }
          } else {
              echo '<p>雇用形態が定義されていません。管理画面でタクソノミーを設定してください。</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 施設形態選択モーダル -->
<div class="modal-overlay" id="facility-type-modal-overlay">
  <div class="modal-wrapper">
    <div class="modal-panel" id="facility-type-selection-modal">
      <div class="modal-header">
        <h3 class="modal-title">施設形態を選択</h3>
        <button type="button" class="modal-close" data-target="facility-type-modal-overlay">&times;</button>
      </div>
      <div class="modal-body">
        <div class="selection-grid">
          <?php
          // facility_type タクソノミーの項目を取得
          $facility_types = get_terms(array(
              'taxonomy' => 'facility_type',
              'hide_empty' => false,
          ));
          
          if (!empty($facility_types) && !is_wp_error($facility_types)) {
              foreach ($facility_types as $facility) {
                  echo '<div class="selection-btn facility-type-btn" data-term-id="' . esc_attr($facility->term_id) . '" data-name="' . esc_attr($facility->name) . '" data-slug="' . esc_attr($facility->slug) . '" data-url="' . esc_url(get_term_link($facility)) . '">' . esc_html($facility->name) . '</div>';
              }
          } else {
              echo '<p>施設形態が定義されていません。管理画面でタクソノミーを設定してください。</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 特徴選択モーダル（親タグを見出しとして表示） -->
<div class="modal-overlay" id="feature-modal-overlay">
  <div class="modal-wrapper">
    <div class="modal-panel" id="feature-selection-modal">
      <div class="modal-header">
        <h3 class="modal-title">特徴を選択</h3>
        <button type="button" class="modal-close" data-target="feature-modal-overlay">&times;</button>
      </div>
      <div class="modal-body">
        <?php
        // job_feature タクソノミーの親タームを取得
        $parent_features = get_terms(array(
            'taxonomy' => 'job_feature',
            'hide_empty' => false,
            'parent' => 0, // 親タームのみ取得
        ));
        
        if (!empty($parent_features) && !is_wp_error($parent_features)) {
            foreach ($parent_features as $parent) {
                // 親タームを見出しとして表示
                echo '<h4 class="feature-category-heading">' . esc_html($parent->name) . '</h4>';
                
                // 親タームの子タームを取得
                $child_features = get_terms(array(
                    'taxonomy' => 'job_feature',
                    'hide_empty' => false,
                    'parent' => $parent->term_id,
                ));
                
                if (!empty($child_features) && !is_wp_error($child_features)) {
                    echo '<div class="feature-checkbox-grid">';
                    foreach ($child_features as $feature) {
                        echo '<label class="feature-checkbox-item">';
                        echo '<input type="checkbox" class="feature-checkbox" data-term-id="' . esc_attr($feature->term_id) . '" data-name="' . esc_attr($feature->name) . '" data-slug="' . esc_attr($feature->slug) . '" data-url="' . esc_url(get_term_link($feature)) . '">';
                        echo '<span class="checkbox-label">' . esc_html($feature->name) . '</span>';
                        echo '</label>';
                    }
                    echo '</div>';
                } else {
                    // 子タームが無い場合は親タームをチェックボックスとして表示
                    echo '<div class="feature-checkbox-grid">';
                    echo '<label class="feature-checkbox-item">';
                    echo '<input type="checkbox" class="feature-checkbox" data-term-id="' . esc_attr($parent->term_id) . '" data-name="' . esc_attr($parent->name) . '" data-slug="' . esc_attr($parent->slug) . '" data-url="' . esc_url(get_term_link($parent)) . '">';
                    echo '<span class="checkbox-label">' . esc_html($parent->name) . '</span>';
                    echo '</label>';
                    echo '</div>';
                }
            }
        } else {
            // 親タームが無い場合は通常通り全ての特徴を表示
            $features = get_terms(array(
                'taxonomy' => 'job_feature',
                'hide_empty' => false,
            ));
            
            if (!empty($features) && !is_wp_error($features)) {
                echo '<div class="feature-checkbox-grid">';
                foreach ($features as $feature) {
                    echo '<label class="feature-checkbox-item">';
                    echo '<input type="checkbox" class="feature-checkbox" data-term-id="' . esc_attr($feature->term_id) . '" data-name="' . esc_attr($feature->name) . '" data-slug="' . esc_attr($feature->slug) . '" data-url="' . esc_url(get_term_link($feature)) . '">';
                    echo '<span class="checkbox-label">' . esc_html($feature->name) . '</span>';
                    echo '</label>';
                }
                echo '</div>';
            } else {
                echo '<p>特徴が定義されていません。管理画面でタクソノミーを設定してください。</p>';
            }
        }
        ?>
        
        <div class="modal-actions right-aligned">
          <button type="button" class="apply-btn" id="apply-features-btn">選択した特徴を適用</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 現在の日付を設定
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1;
    var day = today.getDate();
    $('#update-date').text(year + '年' + month + '月' + day + '日');
    
    // 詳細検索の表示/非表示切り替え
    $('#detail-toggle-btn').on('click', function() {
        var $detailSection = $('.detail-search-section');
        if ($detailSection.is(':visible')) {
            $detailSection.slideUp();
            $(this).text('詳細を指定');
        } else {
            $detailSection.slideDown();
            $(this).text('詳細条件を閉じる');
        }
    });
    
    // 選択フィールドをクリックしたときの処理
    $('#area-field').on('click', function() {
        openModal('area-modal-overlay');
        // 最初のステップを表示
        $('#area-selection-modal').show();
        $('#prefecture-selection-modal').hide();
        $('#city-selection-modal').hide();
    });
    
    $('#position-field').on('click', function() {
        openModal('position-modal-overlay');
    });
    
    $('#job-type-field').on('click', function() {
        openModal('job-type-modal-overlay');
    });
    
    $('#facility-type-field').on('click', function() {
        openModal('facility-type-modal-overlay');
    });
    
    $('#feature-field').on('click', function() {
        // チェックボックスの状態を初期化
        resetFeatureCheckboxes();
        openModal('feature-modal-overlay');
    });
    
    // モーダルを開く
    function openModal(modalId) {
        // すべてのモーダルを非表示にする
        $('.modal-overlay').removeClass('active');
        
        // 指定されたモーダルのみ表示する
        $('#' + modalId).addClass('active');
    }
    
    // モーダルを閉じる
    $('.modal-close').on('click', function() {
        var target = $(this).data('target');
        $('#' + target).removeClass('active'); // activeクラスを削除
    });
    
    // 背景クリックでモーダルを閉じる
    $('.modal-overlay').on('click', function(e) {
        if ($(e.target).is('.modal-overlay')) {
            $(this).removeClass('active'); // activeクラスを削除
        }
    });
    
    // トップレベルのエリア選択時の処理
    $(document).on('click', '.area-btn', function() {
        var termId = $(this).data('term-id');
        var termName = $(this).data('name');
        var termSlug = $(this).data('slug');
        
        // エリア情報を一時保存
        sessionStorage.setItem('selectedAreaId', termId);
        sessionStorage.setItem('selectedAreaName', termName);
        sessionStorage.setItem('selectedAreaSlug', termSlug);
        
        // 選択したエリア名を表示
        $('#selected-area-name').text(termName);
        $('#selected-area-btn-name').text(termName);
        
        // 第2階層のタームをロード
        loadSecondLevelTerms(termId);
        
        // モーダルを切り替え
        $('#area-selection-modal').hide();
        $('#prefecture-selection-modal').fadeIn(300);
    });
    
    // 「全域で検索」ボタン（第1階層）の処理
    $('#select-area-btn').on('click', function() {
        var areaName = sessionStorage.getItem('selectedAreaName');
        var areaSlug = sessionStorage.getItem('selectedAreaSlug');
        var areaId = sessionStorage.getItem('selectedAreaId');
        
        // URLを構築するために使用するTermオブジェクトを取得
        var termUrl = getTermUrl('job_location', areaId);
        
        // 表示テキストを更新
        updateSelectionDisplay('#area-field', areaName);
        
        // hidden inputに値をセット
        $('#location-input').val(areaSlug);
        $('#location-name-input').val(areaName);
        $('#location-term-id-input').val(areaId);
        
        // 第1階層のURLを保存
        sessionStorage.setItem('selectedLocationUrl', termUrl);
        
        // モーダルを閉じる
        $('#area-modal-overlay').removeClass('active');
    });
    
    // 第2階層のターム選択時の処理
    $(document).on('click', '.prefecture-btn', function() {
        var termId = $(this).data('term-id');
        var termName = $(this).data('name');
        var termSlug = $(this).data('slug');
        
        // 都道府県情報を一時保存
        sessionStorage.setItem('selectedPrefectureId', termId);
        sessionStorage.setItem('selectedPrefectureName', termName);
        sessionStorage.setItem('selectedPrefectureSlug', termSlug);
        
        // URLを構築するために使用するTermオブジェクトを取得
        var termUrl = getTermUrl('job_location', termId);
        sessionStorage.setItem('selectedPrefectureUrl', termUrl);
        
        // 選択した都道府県名を表示
        $('#selected-prefecture-name').text(termName);
        $('#selected-prefecture-btn-name').text(termName);
        
        // 第3階層の市区町村タームを取得
        loadThirdLevelTerms(termId);
        
        // モーダルを切り替え
        $('#prefecture-selection-modal').hide();
        $('#city-selection-modal').fadeIn(300);
    });
    
    // 「全域で検索」ボタン（第2階層）の処理
    $('#select-prefecture-btn').on('click', function() {
        var prefectureName = sessionStorage.getItem('selectedPrefectureName');
        var prefectureSlug = sessionStorage.getItem('selectedPrefectureSlug');
        var prefectureId = sessionStorage.getItem('selectedPrefectureId');
        
        // 表示テキストを更新
        updateSelectionDisplay('#area-field', prefectureName);
        
        // hidden inputに値をセット
        $('#location-input').val(prefectureSlug);
        $('#location-name-input').val(prefectureName);
        $('#location-term-id-input').val(prefectureId);
        
        // モーダルを閉じる
        $('#area-modal-overlay').removeClass('active');
    });
    
    // 第3階層のターム選択時の処理
    $(document).on('click', '.city-btn', function() {
        var termId = $(this).data('term-id');
        var termName = $(this).data('name');
        var termSlug = $(this).data('slug');
        var prefectureName = sessionStorage.getItem('selectedPrefectureName');
        
        // URLを構築するために使用するTermオブジェクトを取得
        var termUrl = getTermUrl('job_location', termId);
        
        // 表示テキストを更新
        var displayText = prefectureName + ' ' + termName;
        updateSelectionDisplay('#area-field', displayText);
        
        // hidden inputに値をセット
        $('#location-input').val(termSlug);
        $('#location-name-input').val(displayText);
        $('#location-term-id-input').val(termId);
        
        // 市区町村のURLを保存
        sessionStorage.setItem('selectedLocationUrl', termUrl);
        
        // モーダルを閉じる
        $('#area-modal-overlay').removeClass('active');
    });
    
    // 職種選択時の処理
    $(document).on('click', '.position-btn', function() {
        var termId = $(this).data('term-id');
        var termName = $(this).data('name');
        var termSlug = $(this).data('slug');
        var termUrl = $(this).data('url');
        
        // 表示テキストを更新
        updateSelectionDisplay('#position-field', termName);
        
        // hidden inputに値をセット
        $('#position-input').val(termSlug);
        $('#position-name-input').val(termName);
        $('#position-term-id-input').val(termId);
        
        // URLを一時保存
        sessionStorage.setItem('selectedPositionUrl', termUrl);
        
        // モーダルを閉じる
        $('#position-modal-overlay').removeClass('active');
    });
    
    // 雇用形態選択時の処理
    $(document).on('click', '.job-type-btn', function() {
        var termId = $(this).data('term-id');
        var termName = $(this).data('name');
        var termSlug = $(this).data('slug');
        var termUrl = $(this).data('url');
        
        // 表示テキストを更新
        updateSelectionDisplay('#job-type-field', termName);
        
        // hidden inputに値をセット
        $('#job-type-input').val(termSlug);
        $('#job-type-name-input').val(termName);
        $('#job-type-term-id-input').val(termId);
        
        // URLを一時保存
        sessionStorage.setItem('selectedJobTypeUrl', termUrl);
        
        // モーダルを閉じる
        $('#job-type-modal-overlay').removeClass('active');
    });
    
    // 施設形態選択時の処理
    $(document).on('click', '.facility-type-btn', function() {
        var termId = $(this).data('term-id');
        var termName = $(this).data('name');
        var termSlug = $(this).data('slug');
        var termUrl = $(this).data('url');
        
        // 表示テキストを更新
        updateSelectionDisplay('#facility-type-field', termName);
        
        // hidden inputに値をセット
        $('#facility-type-input').val(termSlug);
        $('#facility-type-name-input').val(termName);
        $('#facility-type-term-id-input').val(termId);
        
        // URLを一時保存
        sessionStorage.setItem('selectedFacilityTypeUrl', termUrl);
        
        // モーダルを閉じる
        $('#facility-type-modal-overlay').removeClass('active');
    });
    
    // 特徴の適用ボタンの処理
    $('#apply-features-btn').on('click', function() {
        var selectedFeatures = [];
        var featureSlugs = [];
        var featureIds = [];
        
        // チェックされた特徴を取得
        $('.feature-checkbox:checked').each(function() {
            var termId = $(this).data('term-id');
            var termName = $(this).data('name');
            var termSlug = $(this).data('slug');
            
            selectedFeatures.push({
                id: termId,
                name: termName,
                slug: termSlug
            });
            
            featureSlugs.push(termSlug);
            featureIds.push(termId);
        });
        
        // 選択した特// 特徴の適用ボタンの処理（続き）
$('#apply-features-btn').on('click', function() {
    var selectedFeatures = [];
    var featureSlugs = [];
    var featureIds = [];
    
    // チェックされた特徴を取得
    $('.feature-checkbox:checked').each(function() {
        var termId = $(this).data('term-id');
        var termName = $(this).data('name');
        var termSlug = $(this).data('slug');
        
        selectedFeatures.push({
            id: termId,
            name: termName,
            slug: termSlug
        });
        
        featureSlugs.push(termSlug);
        featureIds.push(termId);
    });
    
    // 選択した特徴を表示
    updateFeatureSelection(selectedFeatures);
    
    // hidden inputに値をセット
    $('#job-feature-input').val(featureSlugs.join(','));
    
    // モーダルを閉じる
    $('#feature-modal-overlay').removeClass('active');
});

// 戻るボタンの処理
$('.back-btn').on('click', function() {
    var target = $(this).data('target');
    
    // 現在のモーダルを非表示
    $(this).closest('.modal-panel').hide();
    
    // ターゲットモーダルを表示
    $('#' + target).fadeIn(300);
});

// 検索ボタンクリック時の処理
$('#search-btn').on('click', function() {
    handleSearch();
});

// 選択表示の更新
function updateSelectionDisplay(fieldSelector, text) {
    var $field = $(fieldSelector);
    $field.find('.selection-display').text(text);
    $field.find('.selection-display').removeClass('selection-placeholder');
}

// 特徴選択の表示を更新
function updateFeatureSelection(features) {
    var $selectedFeatures = $('#selected-features');
    var $featureField = $('#feature-field');
    
    if (features.length === 0) {
        $featureField.find('.feature-selection-display').text('特徴を選択（複数選択可）');
        $featureField.find('.feature-selection-display').addClass('feature-placeholder');
        $selectedFeatures.empty();
        return;
    }
    
    $featureField.find('.feature-selection-display').text('選択済み：' + features.length + '件');
    $featureField.find('.feature-selection-display').removeClass('feature-placeholder');
    
    $selectedFeatures.empty();
    for (var i = 0; i < features.length; i++) {
        var feature = features[i];
        var $tag = $('<div class="feature-tag">' + feature.name + '</div>');
        $selectedFeatures.append($tag);
    }
}

// 特徴チェックボックスのリセット
function resetFeatureCheckboxes() {
    $('.feature-checkbox').prop('checked', false);
    
    // 現在選択されている特徴に基づいてチェックを復元
    var selectedFeatureSlugs = $('#job-feature-input').val();
    if (selectedFeatureSlugs) {
        var slugs = selectedFeatureSlugs.split(',');
        for (var i = 0; i < slugs.length; i++) {
            $('.feature-checkbox[data-slug="' + slugs[i] + '"]').prop('checked', true);
        }
    }
}

// 第2階層のタームをロードする関数
function loadSecondLevelTerms(parentId) {
    $.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'get_taxonomy_children',
            parent_id: parentId,
            taxonomy: 'job_location'
        },
        success: function(response) {
            if (response.success) {
                displaySecondLevelTerms(response.data);
            } else {
                $('#prefecture-grid').html('<p>階層が見つかりませんでした</p>');
            }
        },
        error: function() {
            $('#prefecture-grid').html('<p>エラーが発生しました</p>');
        }
    });
}

// 第2階層のタームを表示する関数
function displaySecondLevelTerms(terms) {
    var $grid = $('#prefecture-grid');
    $grid.empty();
    
    if (terms.length === 0) {
        $grid.html('<p>該当するエリアがありません</p>');
        return;
    }
    
    for (var i = 0; i < terms.length; i++) {
        var term = terms[i];
        var $btn = $('<div class="prefecture-btn" data-term-id="' + term.term_id + '" data-name="' + term.name + '" data-slug="' + term.slug + '">' + term.name + '</div>');
        $grid.append($btn);
    }
}

// 第3階層のタームをロードする関数
function loadThirdLevelTerms(parentId) {
    $.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'get_taxonomy_children',
            parent_id: parentId,
            taxonomy: 'job_location'
        },
        success: function(response) {
            if (response.success) {
                displayThirdLevelTerms(response.data);
            } else {
                $('#city-grid').html('<p>市区町村が見つかりませんでした</p>');
            }
        },
        error: function() {
            $('#city-grid').html('<p>エラーが発生しました</p>');
        }
    });
}

// 第3階層のタームを表示する関数
function displayThirdLevelTerms(terms) {
    var $grid = $('#city-grid');
    $grid.empty();
    
    if (terms.length === 0) {
        $grid.html('<p>該当する市区町村がありません</p>');
        return;
    }
    
    for (var i = 0; i < terms.length; i++) {
        var term = terms[i];
        var $btn = $('<div class="city-btn" data-term-id="' + term.term_id + '" data-name="' + term.name + '" data-slug="' + term.slug + '">' + term.name + '</div>');
        $grid.append($btn);
    }
}

// タクソノミーのURLを取得する関数
function getTermUrl(taxonomy, termId) {
    var url = '';
    
    $.ajax({
        url: ajaxurl,
        type: 'post',
        async: false, // 同期リクエスト
        data: {
            action: 'get_term_link',
            term_id: termId,
            taxonomy: taxonomy
        },
        success: function(response) {
            if (response.success) {
                url = response.data;
            }
        }
    });
    
    return url;
}

// 検索処理
function handleSearch() {
    var baseUrl = site_url + "/jobs/";
    var filters = [];
    
    // エリア
    var locationSlug = $('#location-input').val();
    if (locationSlug) {
        filters.push('location/' + locationSlug);
    }
    
    // 職種
    var positionSlug = $('#position-input').val();
    if (positionSlug) {
        filters.push('position/' + positionSlug);
    }
    
    // 詳細条件が表示されている場合
    if ($('.detail-search-section').is(':visible')) {
        // 雇用形態
        var jobTypeSlug = $('#job-type-input').val();
        if (jobTypeSlug) {
            filters.push('type/' + jobTypeSlug);
        }
        
        // 施設形態
        var facilityTypeSlug = $('#facility-type-input').val();
        if (facilityTypeSlug) {
            filters.push('facility/' + facilityTypeSlug);
        }
        
        // 特徴（最初の1つのみURLに含める）
        var featureSlugStr = $('#job-feature-input').val();
        if (featureSlugStr) {
            var featureSlugs = featureSlugStr.split(',');
            if (featureSlugs.length > 0) {
                filters.push('feature/' + featureSlugs[0]);
            }
        }
    }
    
    // 選択条件がない場合
    if (filters.length === 0) {
        alert('検索条件を1つ以上選択してください');
        return;
    }
    
    // URLの構築
    var targetUrl = baseUrl + filters.join('/') + '/';
    
    // 検索結果ページに遷移
    window.location.href = targetUrl;
}

// スラッグからタームのURLを取得する関数
function getTermLinkBySlug(taxonomy, slug) {
    var url = '';
    
    $.ajax({
        url: ajaxurl,
        type: 'post',
        async: false, // 同期リクエスト
        data: {
            action: 'get_term_link_by_slug',
            slug: slug,
            taxonomy: taxonomy
        },
        success: function(response) {
            if (response.success) {
                url = response.data;
            }
        }
    });
    
    return url;
}
});
</script>