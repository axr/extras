#!/usr/bin/env ruby

require 'rubygems'
require 'digest'
require 'fileutils'
require 'optparse'

begin
  require 'json'
rescue LoadError
  puts "Please run 'gem install json' first"
  exit!
end

options = {}
OptionParser.new do |opts|
  opts.banner = "Usage: #{File.basename(__FILE__)} [options]"

  opts.on("--input PATH", String, "Path to the packages") do |v|
    options[:input] = v
  end

  opts.on("--data PATH", String, "Data repository location") do |v|
    options[:data] = v
  end
end.parse!

if !options.key?(:input) || !File.directory?(options[:input])
  puts "Invalid input directory"
  exit!
end

if !options.key?(:data) || !File.directory?("#{options[:data]}/.git")
  puts "Invalid data repository location"
  exit!
end

def arch name
  return "x86_64" if name == "amd64"
  return "i386" if ["x86", "i686"].include? name
  return name
end

output = {}
output_dir = "#{options[:data]}/packages/"

Dir.glob("#{options[:input]}/**/*") do |path|
  next unless File.file? path

  basename = File.basename path
  extension = File.extname path

  regex = nil
  regex_named = []

  file_info = nil
  package = nil
  version = nil

  case extension
  when ".deb"
    regex = /^(?<package>[a-z0-9-]+)_(?<version>[0-9.]+)_(?<arch>i386|amd64|all)\.(?<type>deb)$/

  when ".gz", ".zip"
    regex = /^(?<package>[a-z0-9-]+)-(?<version>[0-9.]+)(-(?<os>linux))?-(?<arch>i386|x86_64|src)\.(?<type>tar\.gz|zip)$/

  when ".dmg"
    regex = /^(?<package>[a-z0-9-]+)-(?<version>[0-9.]+)-(?<os>osx)-(?<arch>i386|x86_64|intel)\.(?<type>dmg)$/

  when ".exe", ".msi"
    regex = /^(?<package>[a-z0-9-]+)-(?<version>[0-9.]+)-(?<os>windows)-(?<arch>x86|x64)\.(?<type>msi|exe)$/

  when ".rpm"
    regex = /^(?<package>[a-z0-9-]+)-(?<version>[0-9.]+)-\d+\.(?<arch>i686|x86_64|noarch)\.(?<type>rpm)$/
  end

  if regex.nil?
    puts "Skipping unknown file '#{File.basename(path)}'"
    next
  end

  match = regex.match(basename)
  md = Hash[match.names.zip(match.captures)] unless match.nil?

  next if md.nil?

  package = md["package"]
  version = md["version"]

  file_info = {
    :os => md["os"],
    :arch => arch(md["arch"]),
    :type => md["type"],
    :filename => basename,
    :url => "http://files.axrproject.org/packages/#{package}/#{version}/#{basename}",
    :size => File.size?(path),
    :checksums => {
      :md5 => Digest::MD5.file(path).hexdigest,
      :sha1 => Digest::SHA1.file(path).hexdigest
    }
  }

  file_info[:os] = "linux" if [".deb", ".rpm"].include? extension

  if md["os"] == "osx" and md["arch"] == "universal"
    file_info[:arch] = "intel"
  end

  if md["arch"] == "src"
    file_info[:os] = "src"
    file_info[:arch] = "none"
  end

  if (md["type"] == "deb" and md["arch"] == "all") or (md["type"] == "rpm" and md["arch"] == "noarch")
    file_info[:arch] = "none"
  end

  puts "Found package '#{package}' version '#{version}' for '#{file_info[:os]} #{file_info[:arch]}'"

  output[package] = output[package] || {}
  output[package][version] = output[package][version] || []
  output[package][version].push file_info
end

output.each do |package, versions|
  unless File.directory? "#{output_dir}/#{package}"
    FileUtils.mkdir_p "#{output_dir}/#{package}"
  end

  versions.each do |version, files|
    path = "#{output_dir}/#{package}/release-#{version}.json"

    release_info = {
      :package => package,
      :version => version,
      :files => files
    }

    if File.exist?(path)
      puts "WARNING! Destination file '#{File.basename(path)}' for '#{package}' exists"

      begin
        dest_data = JSON.parse(File.read(path), {:symbolize_names => true})
      rescue JSON::ParserError
        puts "Could not read destination file. Overwriting."
      end

      if dest_data
        puts "Merging destination files."

        release_info[:files] += dest_data[:files]
        filenames = []

        release_info[:files].reject! do |file|
          if filenames.include?(file[:filename])
            true
          else
            filenames.push file[:filename]
            false
          end
        end
      end
    end

    File.open(path, "w") do |file|
      file.write JSON.pretty_generate(release_info, {
        :indent => "\t"
      })
      file.write "\n"
    end
  end
end
