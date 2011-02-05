        <div id="zp-Zotpress" class="wrap">
            
            <?php include('zotpress.tabs.php'); ?>
            
            <div id="zp-Filter">
                <div id="zp-FilterInner">
                
                    <div class="section first">
                        <label for="zp-FilterByAccount">Choose Account:</label>
                        
                        <select id="zp-FilterByAccount">
                        </select>
                        
                        <span id="zp-FilterByAccount-Loading">loading</span>
                    </div>
                    
                    <div class="section">
                        <label for="zp-FilterByCollection">Sort by Collection:</label>
                        
                        <select id="zp-FilterByCollection">
                        </select>
                        
                        <span id="zp-FilterByCollection-Loading">loading</span>
                    </div>
                    
                    <div class="section last">
                        <label for="zp-FilterByLimit">Limit by:</label>
                        
                        <input id="zp-FilterByLimit" type="text" value="5" />
                    </div>
                    
                </div>
            </div>
            
            <div id="zp-List" class="zp-Loading">loading</div>
            
        </div>