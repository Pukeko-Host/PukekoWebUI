<script src="/js/vendor/modernizr-3.8.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.4.1.min.js"><\/script>')</script>
  <script src="/js/plugins.js"></script>
  <script src="/js/main.js?v=5"></script>
  <?php
    global $footerextra;
    if(isset($footerextra)) echo $footerextra;
  ?>
</body>

</html><?php
$conn->close();
?>