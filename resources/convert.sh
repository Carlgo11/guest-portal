cd public/img/bg/
for image in bg.jpg
do
echo "Creating background images..."
convert "$image" "${image%.*}.avif" & convert "$image" -resize 1920x "${image%.*}-lg.avif" & convert "$image" -resize 768x "${image%.*}-md.avif" & convert "$image" -resize 576x "${image%.*}-sm.avif" &
convert "$image" "${image%.*}.webp" & convert "$image" -resize 1920x "${image%.*}-lg.webp" & convert "$image" -resize 768x "${image%.*}-md.webp" & convert "$image" -resize 576x "${image%.*}-sm.webp" &
convert "$image" -resize 1920x "${image%.*}-lg.jpg" & convert "$image" -resize 768x "${image%.*}-md.jpg" & convert "$image" -resize 576x "${image%.*}-sm.jpg" &
wait
echo "Background images created."
done