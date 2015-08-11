jQuery(function() {

	new Nex({
		
		style            : {
			type       : "circle", /* circle, square */
			filter     : "none",  // none, grayscale, sepia, hue-rotate, brightness, contrast, saturate
			pattern    : "", // url to pattern
			background : "#f1c40f", // background color
			hover      : "#f29c13", // hover color of background
			color      : "#000" // text color
		},
	
		data             : [
			{
				display     : "image", // image, map, video
				title       : "Nex", // image title
				description : " &mdash; Blazing Fast Fullscreen Slider", // image description
				link        : "/images/index/1.jpg", // image src
				thumb       : "/images/index/thumb_1.jpg", // image thumb
				url         : "#", // url where image will link
				alt         : "Blazing Fast Fullscreen Slider" // image alt tag
			},
			{
				display     : "image", // image, map, video
				title       : "Nex", // image title
				description : " &mdash; Speed Optimized", // image description
				link        : "/images/index/2.jpg", // image src
				thumb       : "/images/index/thumb_2.jpg", // image thumb
				url         : "#", // url where image will link
				alt         : "Speed Optimized" // image alt tag
			},
			{
				display     : "image", // image, map, video
				title       : "Nex", // image title
				description : " &mdash; GPU Accelerated", // image description
				link        : "/images/index/3.jpg", // image src
				thumb       : "/images/index/thumb_3.jpg", // image thumb
				url         : "#", // url where image will link
				alt         : "GPU Accelerated" // image alt tag
			},
			{
				display     : "image", // image, map, video
				title       : "Nex", // image title
				description : " &mdash; Full Customizable", // image description
				link        : "/images/index/4.jpg", // image src
				thumb       : "/images/index/thumb_4.jpg", // image thumb
				url         : "#", // url where image will link
				alt         : "Full Customizable" // image alt tag
			},
			{
				display     : "image", // image, map, video
				title       : "Nex", // image title
				description : " &mdash; Unique Effects", // image description
				link        : "/images/index/5.jpg", // image src
				thumb       : "/images/index/thumb_5.jpg", // image thumb
				url         : "#", // url where image will link
				alt         : "Unique Effects" // image alt tag
			}
		]
	});
	
});