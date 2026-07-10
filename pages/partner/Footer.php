<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<script>
    function updateTextView(_obj){
        var num = getNumber(_obj.val());
        if(num==0){
            _obj.val('');
        }else{
            _obj.val(num.toLocaleString());
        }
    }
    function getNumber(_str){
        var arr = _str.split('');
        var out = new Array();
        for(var cnt=0;cnt<arr.length;cnt++){
            if(isNaN(arr[cnt])==false){
                out.push(arr[cnt]);
            }
        }
        return Number(out.join(''));
    }
    $(document).ready(function(){
        $('.fnum').on('keyup',function(){
            updateTextView($(this));
        });
    });
</script>
<div id="thongbao"></div>
	<footer class="main">
			&copy; <?=date('Y');?> <strong style="font-weight: bold">Website được vận hành bởi <a href="//dmh.vn">dienmayhieu.com</a></strong>
		</footer>
	</div>
</div>


	<link rel="stylesheet" href="/assets/css/font-icons/font-awesome/css/font-awesome.min.css">

	<link rel="stylesheet" href="/assets/js/zurb-responsive-tables/responsive-tables.css">
	<script src="/assets/js/gsap/TweenMax.min.js"></script>
	<script src="/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="/assets/js/bootstrap.js"></script>
	<script src="/assets/js/joinable.js"></script>
	<script src="/assets/js/resizeable.js"></script>
	<script src="/assets/js/neon-api.js"></script>
	<script src="/assets/js/zurb-responsive-tables/responsive-tables.js"></script>
	<script src="/assets/js/neon-chat.js"></script>
	<script src="/assets/js/neon-demo.js"></script>
	
	<!-- Data-->
	
	<link rel="stylesheet" href="/assets/js/datatables/datatables.css">
	<link rel="stylesheet" href="/assets/js/select2/select2-bootstrap.css">
	<link rel="stylesheet" href="/assets/js/select2/select2.css">
	<script src="/assets/js/datatables/datatables.js"></script>
	<script src="/assets/js/select2/select2.min.js"></script>
	<!--END  Data-->
	
	
	<!-- select -->
	<link rel="stylesheet" href="/assets/js/selectboxit/jquery.selectBoxIt.css">
	<script src="/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="/assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
	<script src="/assets/js/neon-custom.js"></script>
	<!-- end -->

	
	<script src="/assets/js/ckeditor/ckeditor.js"></script>
	<script src="/assets/js/ckeditor/adapters/jquery.js"></script>


	<link rel="stylesheet" href="/assets/js/codemirror/lib/codemirror.css">
	<link rel="stylesheet" href="/assets/js/uikit/addons/css/markdownarea.css">
	<script src="/assets/js/uikit/js/uikit.min.js"></script>
	<script src="/assets/js/codemirror/lib/codemirror.js"></script>
	<script src="/assets/js/marked.js"></script>
	<script src="/assets/js/uikit/addons/js/markdownarea.min.js"></script>
</body>
</html>
