<div class="aqar-wrap">
        <h1>الشركات العقارية</h1>
        <p>فيما يلي قائمة بمستخدمي الوكالة مع تحديث أسمائهم الأولى والأخيرة من البيانات التعريفية.</p>
        <button id="fetch-agency-users" class="button button-primary">تحديث مستخدمي الوكالة</button>
        <div id="loading" style="display: none;">
            <div class="svg-loader">
                <svg class="svg-container" height="50" width="50" viewBox="0 0 100 100">
                    <circle class="loader-svg bg" cx="50" cy="50" r="45"></circle>
                    <circle class="loader-svg animate" cx="50" cy="50" r="45"></circle>
                </svg>
            </div>
        </div>
        <div id="agency-users-table" style="margin-top: 20px; display: none;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Display Name</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody id="agency-users-body"></tbody>
            </table>
        </div>
    </div>