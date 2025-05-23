<?php

// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: Message.proto

namespace App\Protobuf\GPBMetadata;

class Message
{
    public static $is_initialized = false;

    public static function initOnce()
    {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (true == static::$is_initialized) {
            return;
        }
        $pool->internalAddGeneratedFile(
            '

Message.protoApp.Protobuf"@
TaskMessage 
clip (2.App.Protobuf.Clip
service (	"J
SoundExtractorMessage 
clip (2.App.Protobuf.Clip
service (	"K
VideoDownloaderMessage 
clip (2.App.Protobuf.Clip
service (	"M
SubtitleGeneratorMessage 
clip (2.App.Protobuf.Clip
service (	"J
SubtitleMergerMessage 
clip (2.App.Protobuf.Clip
service (	"O
SubtitleTransformerMessage 
clip (2.App.Protobuf.Clip
service (	"O
SubtitleIncrustatorMessage 
clip (2.App.Protobuf.Clip
service (	"J
VideoFormatterMessage 
clip (2.App.Protobuf.Clip
service (	"L
VideoIncrustatorMessage 
clip (2.App.Protobuf.Clip
service (	"I
VideoSplitterMessage 
clip (2.App.Protobuf.Clip
service (	"
Clip

id (	
userId (	
status (	*
originalVideo (2.App.Protobuf.Video
cover (	H 2
configuration (2.App.Protobuf.Configuration0
processedVideo (2.App.Protobuf.VideoH
url (	HB
_coverB
_processedVideoB
_url"
Video

id (	
originalName (	H 
name (	H
mimeType (	H
size (H
length (H
subtitle (	H
ass (	H
	subtitles	 (	
audios
 (	B
_originalNameB
_nameB
	_mimeTypeB
_sizeB	
_lengthB
	_subtitleB
_ass"δ
Configuration

id (	
subtitleFont (	
subtitleSize (	
subtitleColor (	
subtitleBold (	
subtitleItalic (	
subtitleUnderline (	
subtitleOutlineColor (	 
subtitleOutlineThickness	 (	
subtitleShadow
 (	
subtitleShadowColor (	
format (	
split (	
marginV (	
privacyOptions (	*t
ConfigurationSubtitleShadow
SHADOW_NONE 
SHADOW_SOFT
SHADOW_MEDIUM
SHADOW_BOLD
SHADOW_HARD*
%ConfigurationSubtitleOutlineThickness
OUTLINE_NONE 
OUTLINE_SOFT
OUTLINE_MEDIUM
OUTLINE_BOLD
OUTLINE_HARD*L
ConfigurationSubtitleFont	
ARIAL 
TIMES_NEW_ROMAN
COURIER_NEW*i
VideoFormatStyle
ORIGINAL 

ZOOMED_916
NORMAL_916_WITH_BORDERS
DUPLICATED_BLURRED_916*
SocialAccountType

TIKTOK *=
VideoPublishStatus
	UPLOADING 
	PUBLISHED	
ERROR*

ClipStatus
UPLOADED 
SOUND_EXTRACTOR_PENDING
SOUND_EXTRACTOR_COMPLETE
SOUND_EXTRACTOR_ERROR
SUBTITLE_GENERATOR_PENDING
SUBTITLE_GENERATOR_COMPLETE
SUBTITLE_GENERATOR_ERROR
SUBTITLE_MERGER_PENDING
SUBTITLE_MERGER_COMPLETE
SUBTITLE_MERGER_ERROR	 
SUBTITLE_TRANSFORMER_PENDING
!
SUBTITLE_TRANSFORMER_COMPLETE
SUBTITLE_TRANSFORMER_ERROR
VIDEO_FORMATTER_PENDING
VIDEO_FORMATTER_COMPLETE
VIDEO_FORMATTER_ERROR 
SUBTITLE_INCRUSTATOR_PENDING!
SUBTITLE_INCRUSTATOR_COMPLETE
SUBTITLE_INCRUSTATOR_ERROR
VIDEO_SPLITTER_PENDING
VIDEO_SPLITTER_COMPLETE
VIDEO_SPLITTER_ERROR
VIDEO_INCRUSTATOR_PENDING
VIDEO_INCRUSTATOR_COMPLETE
VIDEO_INCRUSTATOR_ERROR

CLIP_READY
CLIP_UPLOADING
CLIP_UPLOADED
STATUS_ERROR
VIDEO_DOWNLOADER_PENDING
VIDEO_DOWNLOADER_COMPLETE
VIDEO_DOWNLOADER_ERRORB*ΚApp\\ProtobufβApp\\Protobuf\\GPBMetadatabproto3', true);

        static::$is_initialized = true;
    }
}
