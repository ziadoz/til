module ApplicationHelper
  def build_page_title(*crumbs)
    crumbs.map(&:to_s).reject(&:empty?).join(' - ')
  end
end
