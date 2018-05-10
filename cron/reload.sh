echo "loading"
pid=`pidof im_master`
echo $pid
kill -USR1 $pid
echo "loading success"