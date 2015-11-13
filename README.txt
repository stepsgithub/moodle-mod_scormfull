安裝步驟：
1. 請將scormfull資料夾放置在moodle資料夾位置的mod之內。
2. 請將scormfull內的quiz/view.php覆蓋mod/quiz的view.php，覆蓋前請先把mod/quiz/view.php備份為view.php.bak。
3. 點選 網站管理 > 通知  => 確認安裝成功
4. 如安裝正常可以進入課程新增線上資源，選擇『SCORM考試』，設定在進行試題前需要完成SCORM/AICC 課程包完成門檻。

使用說明：
1. 新增『SCORM考試』前，在該課程底下需要有試題、SCORM/AICC 課程包。
2. 『SCORM考試』欄位解釋:
        a.測驗卷: 設定該測驗卷在測驗前需要達成SCORM/AICC 課程包完成度。
        b.SCORM/AICC 課程包: 需完成該SCORM/AICC 課程包完成度，才可進行測驗。
        c.SCORM/AICC 課程包完成度: SCORM/AICC 課程包本身會依據評分/課程分段的需求，分為不同的節點，計算使用者完成SCORM/AICC 課程包的節點數除以SCORM/AICC 課程包的總節>點數作為該使用者完成SCORM/AICC 課程包的完成度，用以衡量是否通過SCOROM考試的門檻。
        b.加入參考課程標準設定:是否加入該使用者在SCORM/AICC 課程包停留時間，作為衡量通過『SCORM考試』的門檻。
        e.小時數: moodle中所有的活動記錄在資料庫中，在需要的功能可以作為列表、統計。因此採用該記錄計算使用者在SCORM/AICC停留的小時數。
        f.考試成績：用來衡量，使用者是否有達到考試成績設定。
3. 如須知道該使用者是否通過考試，點選『SCORM考試』所建立在課程上的節點中。
4. 須有教師權限才可了解該『SCORM考試』,SCORM/AICC 課程包所有使用者的完成度,小時數。

此專案由 宜蘭縣政府教育處(http://www.ilc.edu.tw/) 提供
委託 智新資通服份有限公司(http://www.steps.com.tw/) 開發
