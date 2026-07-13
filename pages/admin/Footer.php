        </main>
    </div>
</div>
<script src="/assets/js/jquery-1.11.3.min.js"></script>
<script>
$(document).ready(function(){
    $('.admin-menu li:has(.admin-submenu) > a').on('click', function(e){
        e.preventDefault();
        $(this).next('.admin-submenu').toggleClass('open');
    });
});
</script>
</body>
</html>
