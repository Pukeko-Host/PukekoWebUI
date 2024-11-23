<script src="/js/vendor/modernizr-3.8.0.min.js"></script>
  <script src="https://cdn.yiays.com/jquery-3.7.1.min.js"></script>
  <script src="/js/plugins.js"></script>
  <script src="/js/main.js?v=7"></script>
  <?php
    global $footerextra;
    if(isset($footerextra)) echo $footerextra;
  ?>
</body>

</html><?php
$conn->close();
?>